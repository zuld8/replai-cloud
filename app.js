import 'dotenv/config'
import express from 'express'
import nodeCleanup from 'node-cleanup'
import routes from './waserver/routes.js'
import { init, cleanup } from './waserver/whatsapp.js'
import cors from 'cors'

const app = express()

const host = process.env.WHATSAPP_HOST || undefined
const port = parseInt(process.env.WHATSAPP_PORT ?? 8000)

app.use(cors())
app.use(express.urlencoded({ extended: true }))
app.use(express.json())
app.use('/', routes)

const listenerCallback = () => {
    init() 
}

if (host) {
    app.listen(port, host, listenerCallback)
} else {
    app.listen(port, listenerCallback)
}

nodeCleanup(cleanup)

export default app
