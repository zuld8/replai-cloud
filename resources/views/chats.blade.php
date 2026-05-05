<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <meta charset="UTF-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <title>{{$page}}</title>
    <x-meta-component></x-meta-component>
    <link href="{{asset('assets/css/livechat-output.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('assets/css/livechat.css')}}" />
</head>

<body id="chat-widget-container">
    <div
        id="chat-bubble"
        class="w-16 h-16 bg-primary rounded-full flex items-center justify-center cursor-pointer text-3xl hover:bg-blue-800 transition-colors duration-200 overflow-hidden relative">
        <img
            id="profile-image"
            src="#"
            alt="Profile Image"
            style="display: none;" />
        <svg
            id="chat-icon"
            xmlns="http://www.w3.org/2000/svg"
            class="w-10 h-10 text-white absolute"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2">
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
    </div>
    <div
        id="chat-popup"
        class="absolute bottom-20 right-0 w-[392px] h-128 bg-white border border-slate-100 rounded-lg shadow-lg flex flex-col transition-all text-sm">
        <div
            id="chat-header"
            class="flex bottom-shadow justify-between border-none items-center p-4 bg-white rounded-t-lg">
            <div class="profile-section">
                <img
                    id="profile-image"
                    src="{{asset('images/user.png')}}"
                    alt="Profile Image" />
                <div class="profile-info">
                    <h3 id="inbox-name"></h3>
                </div>
            </div>
            <button
                id="close-popup"
                class="bg-transparent border-none text-gray-400 hover:text-gray-600 cursor-pointer p-2 rounded-full hover:bg-gray-100 transition-all">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="user-info-form" class="user-info-form absolute inset-0 flex items-center justify-center bg-white">
            <div class="w-full px-2">
                <div class="form-header text-center mb-8">
                    <h2 class="text-2xl font-semibold mb-2">Start Chatting</h2>
                    <p class="text-gray-600">Talk to our online agent</p>
                </div>
                <form id="contact-form" class="w-full max-w-[360px] mx-auto">
                    <div class="form-group mb-5">
                        <label for="user-name">Name</label>
                        <input
                            type="text"
                            id="user-name"
                            placeholder="Enter your name"
                            required
                            class="w-full">
                    </div>
                    <div class="form-group phone-input-group mb-8">
                        <label for="user-phone">Phone Number</label>
                        <div class="phone-input-container">

                            <input
                                type="tel"
                                id="user-phone"
                                placeholder="62xx xxxx xxxx"
                                required
                                class="w-full">
                        </div>
                    </div>
                    <button type="submit" class="start-chat-btn w-full">
                        Start Chat
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                            <path d="M3.105 2.289a.75.75 0 00-.826.95l1.414 4.925A1.5 1.5 0 005.135 9.25h6.115a.75.75 0 010 1.5H5.135a1.5 1.5 0 00-1.442 1.086l-1.414 4.926a.75.75 0 00.826.95 28.896 28.896 0 0015.293-7.154.75.75 0 000-1.115A28.897 28.897 0 003.105 2.289z" />
                        </svg>
                    </button>
                </form>
                <div class="powered-by text-center mt-8 text-sm text-gray-500">
                    Powered by <a href="https://replai.org/replai-pro" target="_blank" class="text-blue-600">replai.org</a>
                </div>
            </div>
        </div>

        <div
            id="chat-messages"
            class="flex-1 p-4 overflow-y-auto bg-gray-100 "></div>
        <div class="bg-gray-100">
            <div id="typing-indicator">Agent is typing...</div>
        </div>
        <div id="inbox-faq" class="flex overflow-x-auto py-2 bg-gray-100  space-x-2 px-2 whitespace-nowrap">

            <!-- inbox-faq -->
        </div>


        <div
            id="chat-input-container"
            class="p-4 top-shadow border-t text-base border-gray-200">
            <div class="flex items-center">
                <textarea
                    id="chat-input"
                    class="flex-1 bg-transparent rounded-md px-2 py-2 outline-none w-3/4"
                    placeholder="Type your message..."
                    rows="1"></textarea>
                <button id="upload-button" class="cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" id="plus">
                        <g fill="none" fill-rule="evenodd" stroke="#200E32" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" transform="translate(2 2)">
                            <line x1="10" x2="10" y1="6.327" y2="13.654"></line>
                            <line x1="13.667" x2="6.333" y1="9.99" y2="9.99"></line>
                            <path d="M14.6857143,0 L5.31428571,0 C2.04761905,0 0,2.31208373 0,5.58515699 L0,14.414843 C0,17.6879163 2.03809524,20 5.31428571,20 L14.6857143,20 C17.9619048,20 20,17.6879163 20,14.414843 L20,5.58515699 C20,2.31208373 17.9619048,0 14.6857143,0 Z"></path>
                        </g>
                    </svg>
                </button>
                <button
                    id="chat-submit"
                    class="text-white rounded-md px-4 py-2 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16pt" height="16pt" viewBox="0 0 64 64" style="isolation:isolate" id="send">
                        <defs>
                            <clipPath id="a">
                                <rect width="64" height="64"></rect>
                            </clipPath>
                        </defs>
                        <g clip-path="url(#a)">
                            <path d=" M 8.216 36.338 L 26.885 32.604 C 28.552 32.271 28.552 31.729 26.885 31.396 L 8.216 27.662 C 7.104 27.44 6.021 26.356 5.799 25.245 L 2.065 6.576 C 1.731 4.908 2.714 4.133 4.259 4.846 L 61.228 31.139 C 62.257 31.614 62.257 32.386 61.228 32.861 L 4.259 59.154 C 2.714 59.867 1.731 59.092 2.065 57.424 L 5.799 38.755 C 6.021 37.644 7.104 36.56 8.216 36.338 Z "></path>
                        </g>
                    </svg>
                </button>


            </div>
            <div class="flex text-center text-xs pt-4">
                <span class="flex-1">Powered by
                    <a
                        href="https://replai.org/"
                        target="_blank"
                        class="text-indigo-600">replai.org</a></span>
            </div>
        </div>
    </div>
    <script>
        window.appConfig = {
            appUrl: @json(config('app.url')),
            socketUrl: @json(config('custom.socket_url'))
        };
    </script>
    <script src="{{asset('assets/js/livechat.js')}}" type="module"></script>
</body>

</html>