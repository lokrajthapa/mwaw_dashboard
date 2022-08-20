require('dotenv').config()
const fs = require('fs')
const connectedcar = require('connected-car')

const express = require('express')
const app = express()
const port = process.env.FORDPASS_PORT || 4000

const client = connectedcar.AuthClient('9fb503e0-715b-47e8-adfd-ad4b7770f73b', {region: 'CA'})
const filePath = 'fordpass-token.json';

async function getToken() {
    if (!fs.existsSync(filePath)) {
        console.log('getting token from credentials')
        const token = await client.getAccessTokenFromCredentials({
            username: process.env.FORDPASS_USERNAME,
            password: process.env.FORDPASS_PASSWORD,
        })
        fs.writeFileSync(filePath, JSON.stringify(token), {encoding: 'utf-8'})
        return token
    } else {
        const token = JSON.parse(fs.readFileSync(filePath, {encoding: 'utf-8'}))
        if (isTokenExpired(token)) {
            console.log('token expired')
            const refreshToken = await client.getAccessTokenFromRefreshToken(token.refreshToken)
            console.log('refresh token')
            fs.writeFileSync(filePath, JSON.stringify(refreshToken), {encoding: 'utf-8'})
            return refreshToken
        } else {
            console.log('token not expired')
            return token
        }
    }
}

function isTokenExpired(token) {
    return new Date(token.expiresAt) < Date.now();

}

app.get('/status', async (req, res) => {
    try {
        const token = await getToken()
        const vehicle = connectedcar.Vehicle(req.query.vin, token.value, 'CA')
        const status = await vehicle.status()
        return res.json({status})
    } catch (e) {
        console.log(e)
        return res.json({error: e})
    }
})

app.get('/', async (req, res) => {
    res.json({message: 'hello world'})
})

app.listen(port, () => {
    console.log(`Fordpass server running on port ${port}`)
})



