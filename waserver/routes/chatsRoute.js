import { Router } from 'express'
import { body, query } from 'express-validator'
import requestValidator from '../middlewares/requestValidator.js'
import sessionValidator from '../middlewares/sessionValidator.js'
import * as controller from '../controllers/chatsController.js'
import getMessages from '../controllers/getMessages.js'

const router = Router()

router.get('/', query('id').notEmpty(),query('isgroup').notEmpty(), requestValidator, sessionValidator, controller.getList)
router.get('/check-number', query('id').notEmpty(), requestValidator, sessionValidator, controller.checkingNumber)
router.get('/get-contacts', query('id').notEmpty(), requestValidator, sessionValidator, controller.getContacts)
router.get('/:jid', query('id').notEmpty(), requestValidator, sessionValidator, getMessages)
router.post(
    '/send',
    query('id').notEmpty(),
    body('receiver').notEmpty(),
    body('message').notEmpty(),
    body('isgroup').notEmpty(),
    requestValidator,
    sessionValidator,
    controller.send
)

router.post(
    '/download-media',
    query('id').notEmpty(), 
    body('message').notEmpty(),
    requestValidator,
    sessionValidator,
    controller.downloadMedia
)

router.post(
    '/get-profile',
    query('id').notEmpty(), 
    body('phone').notEmpty(),
    requestValidator,
    sessionValidator,
    controller.getPhotoProfile
)

router.post(
    '/read-messages',
    query('id').notEmpty(), 
    body('chatid').notEmpty(),
    body('messages').notEmpty(),
    requestValidator,
    sessionValidator,
    controller.readBulkMessage
)

router.post(
    '/mark-message',
    query('id').notEmpty(), 
    body('chatid').notEmpty(),
    body('status').notEmpty(),
    requestValidator,
    sessionValidator,
    controller.markToChat
)

router.post(
    '/delete-message',
    query('id').notEmpty(), 
    body('chatid').notEmpty(),
    body('message').notEmpty(),
    requestValidator,
    sessionValidator,
    controller.messageDelete
)

router.post(
    '/delete-everyone',
    query('id').notEmpty(), 
    body('chatid').notEmpty(),
    body('message').notEmpty(),
    requestValidator,
    sessionValidator,
    controller.deleteMessageEveryone
)

router.post(
    '/delete-chat',
    query('id').notEmpty(), 
    body('chatid').notEmpty(),
    requestValidator,
    sessionValidator,
    controller.chatDelete
)

router.post('/send-bulk', query('id').notEmpty(), requestValidator, sessionValidator, controller.sendBulk)

export default router
