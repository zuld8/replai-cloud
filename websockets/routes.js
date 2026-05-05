import { Router } from 'express'
import sessionsRoute from './routes/chatRoutes.js' 
import response from './response.js'

const router = Router()

router.use('/chats', sessionsRoute) 

router.all('*', (req, res) => {
    response(res, 404, false, 'The requested url cannot be found.')
})

export default router
