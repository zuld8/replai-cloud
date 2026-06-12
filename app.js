import 'dotenv/config'
import express from 'express'
import nodeCleanup from 'node-cleanup'
import routes from './waserver/routes.js'
import { init, cleanup } from './waserver/whatsapp.js'
import cors from 'cors'
import apiKeyAuth from './waserver/middlewares/apiKeyAuth.js'

const app = express()

// Bind to localhost by default — never expose waserver directly to internet
const host = process.env.WHATSAPP_HOST || '127.0.0.1'
const port = parseInt(process.env.WHATSAPP_PORT ?? 8000)

// Restrict CORS to trusted origins only
const allowedOrigins = process.env.WASERVER_ALLOWED_ORIGINS
    ? process.env.WASERVER_ALLOWED_ORIGINS.split(',').map(o => o.trim())
    : ['https://chat.replai.id']

app.use(cors({ origin: allowedOrigins }))
app.use(express.urlencoded({ extended: true }))
app.use(express.json())

// API key authentication — all waserver routes require x-api-key header
app.use(apiKeyAuth('WASERVER_API_KEY'))

app.use('/', routes)

const listenerCallback = () => {
    init()
}

app.listen(port, host, listenerCallback)

nodeCleanup(cleanup)

export default app

