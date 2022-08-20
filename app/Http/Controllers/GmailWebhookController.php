<?php

namespace App\Http\Controllers;

use App\Google\GmailParser;
use App\Google\Google;
use App\Jobs\SendSmsJob;
use App\Models\Sms;
use Google\Service\Gmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GmailWebhookController extends Controller
{
    /**
     * @throws \Google\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function webhook(Request $request)
    {
//        Log::debug('email webhook', $request->all());
        $historyId = json_decode(base64_decode($request->message['data']), true)['historyId'];
//        Log::debug('historyId', [$historyId]);

        $lastHistoryId = Cache::store('redis')->get('lastGmailHistoryId');
        Cache::store('redis')->set('lastGmailHistoryId', $historyId);

        if ($lastHistoryId) {
            $parsedGmails = $this->parseGmail($lastHistoryId);
            if ($parsedGmails) {
                $this->replySms($parsedGmails);
            }

//            Log::debug('parsed gmails', $parsedGmails);
        }


        return response()->json(['message' => 'success']);
    }

    /**
     * @throws \Google\Exception
     */
    public function parseGmail($historyId): array
    {
        $client = Google::getClient();
        $gmail = new Gmail($client);
        $historyResponse = $gmail->users_history->listUsersHistory('me', ['startHistoryId' => $historyId]);
        $histories = $historyResponse->getHistory();

        $output = [];

        foreach ($histories as $history) {
            $messagesAdded = $history->getMessagesAdded();
            if ($messagesAdded) {
                foreach ($messagesAdded as $messageAdded) {
                    $message = $gmail->users_messages->get('me', $messageAdded->getMessage()->id);
                    $payload = $message->getPayload();

                    $headers = $payload->getHeaders();
                    $originalMessageId = null;
                    foreach ($headers as $header) {
                        if ($header->name == 'References') {
                            $references = $header->value;
                            if (str_contains($references, ' ')) {
                                $originalMessageId = explode(' ', $references)[0];
                            } else {
                                $originalMessageId = $references;
                            }
                            break;
                        }
                    }

                    $parts = $payload->getParts();
                    if ($parts) {
                        $body = $parts[0]->getBody();
                        $content = base64_decode(strtr($body->getData(), '-_', '+/'));
                        $output[] = [
                            'messageId' => $originalMessageId,
                            'message' => GmailParser::parse($content)
                        ];
                    }
                }
            }
        }

        return $output;
    }

    public function replySms($gmails)
    {
        foreach ($gmails as $gmail) {
            $sms = Sms::query()->where('email_id', $gmail['messageId'])->where('type', 'job')->first();
            if ($sms) {
                $body = $sms->body;
                $body .= '++++++++++++++++++';
                $body .= $gmail['message'];
                $sms->body = $body;
                $sms->save();
//                Log::debug('reply sms',[$body]);
                SendSmsJob::dispatch($sms->to, $sms->from, $body);
            }
        }
    }


}
