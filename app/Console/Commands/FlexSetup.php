<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Twilio\Rest\Client;
use Twilio\Rest\Taskrouter\V1\Workspace\TaskQueueInstance;
use Twilio\Rest\Taskrouter\V1\Workspace\WorkflowInstance;
use Twilio\Rest\Taskrouter\V1\WorkspaceContext;

class FlexSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:setup-flex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public Client $client;
    public WorkspaceContext $workspace;

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function handle()
    {
        $this->client = new Client(env('TWILIO_FLEX_ACCOUNT_SID'), env('TWILIO_FLEX_AUTH_TOKEN'));
        $this->workspace = $this->client->taskrouter->workspaces(env('TWILIO_FLEX_WORKSPACE_SID'));

//        $this->setupTaskQueues();
//        $this->setupWorkers();
        $this->setupWorkflows();
        return 0;
    }

    /**
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function setupTaskQueues()
    {
        $queueTemplates = [
            'Everyone' => [
                'targetWorkers' => '1==1',
                'maxReservedWorkers' => 3,
            ],
            'CallbackandVoicemailQueue' => [
                'targetWorkers' => '1==0',
                'maxReservedWorkers' => 1,

            ],
            'Booking' => [
                'targetWorkers' => 'categories HAS "Booking"',
                'maxReservedWorkers' => 3,

            ],
            'Update Existing' => [
                'targetWorkers' => 'categories HAS "Update Existing"',
                'maxReservedWorkers' => 3,

            ],
            'Customer Service' => [
                'targetWorkers' => 'categories HAS "Customer Service"',
                'maxReservedWorkers' => 3,

            ],
            'General Inquiries' => [
                'targetWorkers' => 'categories HAS "General Inquiries"',
                'maxReservedWorkers' => 3,

            ]
        ];

        $queues = $this->workspace->taskQueues->read();

        foreach ($queueTemplates as $template => $data) {
            $new = true;
            foreach ($queues as $queue) {
                if ($template == $queue->friendlyName) {
                    $this->info('Update ' . $template);
                    $queue->update($data);
                    $new = false;
                }
            }
            if ($new) {
                $this->info('Create ' . $template);
                $this->workspace->taskQueues->create($template, $data);
            }
        }
    }

    /**
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function setupWorkers()
    {
        $workerTemplates = [
            'Dean' => [
                'categories' => ['General Inquiries', 'Booking', 'Update Existing', 'Customer Service'],
                'general_inquiries' => 100,
                'booking' => 100,
                'update_existing' => 100,
                'customer_service' => 100,
            ],
            'saurav@manwithawrench.com' => [
                'categories' => [],
            ],
            'alex@manwithawrench.com' => [
                'categories' => [],
            ],
            'cristina@manwithawrench.com' => [
                'categories' => ['General Inquiries', 'Customer Service', 'Booking', 'Update Existing'],
                'general_inquiries' => 50,
                'customer_service' => 100,
                'booking' => 50,
                'update_existing' => 75
            ],
            'chris@manwithawrench.com' => [
                'categories' => ['General Inquiries', 'Booking', 'Update Existing'],
                'general_inquiries' => 100,
                'booking' => 100,
                'update_existing' => 100,
            ],
            'aaliyah@manwithawrench.com' => [
                'categories' => ['General Inquiries', 'Customer Service', 'Booking', 'Update Existing'],
                'general_inquiries' => 100,
                'customer_service' => 100,
                'booking' => 100,
                'update_existing' => 100,

            ],
            'erika@manwithawrench.com' => [
                'categories' => ['General Inquiries', 'Booking', 'Update Existing'],
                'general_inquiries' => 100,
                'booking' => 100,
                'update_existing' => 100,
            ],
            'lilia@manwithawrench.com' => [
                'categories' => ['General Inquiries', 'Customer Service', 'Booking'],
                'general_inquiries' => 50,
                'customer_service' => 50,
                'booking' => 50,
                'update_existing' => 50,

            ],
            'tameka@manwithawrench.com' => [
                'categories' => ['General Inquiries', 'Booking', 'Update Existing'],
                'general_inquiries' => 100,
                'booking' => 100,
                'update_existing' => 100,
            ],
            'oleg@manwithawrench.com' => [
                'categories' => [],
            ],
        ];

        $workers = $this->workspace->workers->read();


        foreach ($workerTemplates as $template => $data) {
            $new = true;
            foreach ($workers as $worker) {
                if ($template == $worker->friendlyName) {
                    $this->info('Update ' . $template);
                    $parsedAttributes = json_decode($worker->attributes, true);
                    $mergedAttributes = array_merge($parsedAttributes, $data);

                    $worker->update(['attributes' => json_encode($mergedAttributes)]);
                    $new = false;

                    $workerChannels = $this->workspace->workers($worker->sid)->workerChannels->read();
                    foreach ($workerChannels as $channel) {
                        if (in_array($channel->taskChannelUniqueName,
                            ['default', 'chat', 'email', 'sms', 'callback', 'voicemail'])) {
                            $channel->update(['capacity' => 5]);
                        }
                    }
                }
            }
            if ($new) {
                $this->info('Create ' . $template);
                $this->workspace->workers->create($template, ['attributes' => json_encode($data)]);
            }
        }
    }

    /**
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function setupWorkflows()
    {
        $queues = collect(Cache::store('redis')->remember('cachedQueues', 240, function () {
            return $this->workspace->taskQueues->read();
        }));
        $keyedQueues = $queues->mapWithKeys(function (TaskQueueInstance $instance) {
            return [$instance->friendlyName => $instance->sid];
        });

        $configuration = [
            'task_routing' => [
                'filters' => [
                    ['filter_friendly_name' => 'Booking',
                        'expression' => 'mainSelection=="1"',
                        'targets' => [
                            [
                                'queue' => $keyedQueues['Booking'],
                                'order_by' => "worker.booking DESC",
                            ]
                        ]
                    ],

                    ['filter_friendly_name' => 'Update Existing',
                        'expression' => 'mainSelection=="2"',
                        'targets' => [
                            [
                                'queue' => $keyedQueues['Update Existing'],
                                'order_by' => "worker.update_existing DESC",
                            ]
                        ]
                    ],

                    ['filter_friendly_name' => 'Customer Service',
                        'expression' => 'mainSelection=="3"',
                        'targets' => [
                            [
                                'queue' => $keyedQueues['Customer Service'],
                                'order_by' => "worker.customer_service DESC",
                            ]
                        ]
                    ],

                    ['filter_friendly_name' => 'General Inquiries',
                        'expression' => 'mainSelection=="4"',
                        'targets' => [
                            [
                                'queue' => $keyedQueues['General Inquiries'],
                                'order_by' => "worker.general_inquiries DESC",
                            ]
                        ]
                    ],

                ],
                'default_filter' => [
                    'queue' => $keyedQueues['Everyone']
                ]
            ]
        ];

//        dd($configuration);

        $workflows = $this->workspace->workflows->read();

        $keys = array_map(function (WorkflowInstance $workflowInstance) {
            return $workflowInstance->friendlyName;
        }, $workflows);

        if (!in_array('Custom Workflow', $keys)) {
            $this->info('Create custom workflow');
            $this->workspace->workflows->create('Custom Workflow', json_encode($configuration));
        } else {
            $this->info('Update workflow');
            $customWorkflow = array_values(array_filter($workflows, function (WorkflowInstance $workflowInstance) {
                return $workflowInstance->friendlyName == 'Custom Workflow';
            }))[0];
            $customWorkflow->update(['configuration' => json_encode($configuration)]);
        }
    }
}
