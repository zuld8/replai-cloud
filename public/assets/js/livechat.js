import { io } from "./livechat-socket.js";
const form = document.getElementById("form");

// Inside the iframe script
const urlParams = new URLSearchParams(window.location.search);
const accessKey = urlParams.get("chat"); // Retrieve the accessKey

//TODO CHANGE SOCKET CONNECTION FOR DEVELOPMENT 
const socketConnection = (window.appConfig?.socketUrl || '');
const webServer = (window.appConfig?.appUrl || '') + "/api-app";

let socket;
let savedSocketId;
let conversationId;
let contactId;
let inboxData;
let displayedMessageIds = new Set();
let isHistoryFetched = false;
let isSocketConnected = false;

document.addEventListener("DOMContentLoaded", () => {
    const chatBubble = document.getElementById("chat-bubble");
    const chatPopup = document.getElementById("chat-popup");
    const closePopupButton = document.getElementById("close-popup");
    const chatInput = document.getElementById("chat-input");
    const chatSubmit = document.getElementById("chat-submit");
    const chatMessages = document.getElementById("chat-messages");
    const svgPath = chatBubble.querySelector("svg path");
    const typingIndicator = document.getElementById("typing-indicator");
    const inboxFaq = document.getElementById("inbox-faq");
    let selectedFile = null; // Variable to store the selected file

    const userInfoForm = document.getElementById("user-info-form");
    const contactForm = document.getElementById("contact-form");
    const chatMessagesContainer = document.getElementById("chat-messages");

    // Remove the immediate socket connection check and just set up the UI state
    const storedContactId = localStorage.getItem("idcontact");

    // Define connectSocket function first
    const connectSocket = (userData) => {
        if (isSocketConnected) return;

        socket = io(socketConnection, {
            withCredentials: true,
            auth: { serverOffset: 0 },
            query: { token: accessKey },
            transports: ["websocket"],
        });

        socket.on("connect", () => {
            // console.log("Socket connected");
            isSocketConnected = true;
            const storedAccessKeyData2 = JSON.parse(
                localStorage.getItem(accessKey)
            );
            const storedConversationId = storedAccessKeyData2?.conversation_id;
            const storedContactId = storedAccessKeyData2?.idcontact;
            savedSocketId = storedAccessKeyData2?.socket_id; 

            const storedSocketId = localStorage.getItem("socket_id");

            if (
                !savedSocketId &&
                !storedSocketId &&
                !storedAccessKeyData2?.socket_id
            ) {
                // console.log("blm ada socket id", socket.id)
                savedSocketId = socket.id;
            }

            if (storedSocketId) {
                // console.log("adaaa socket lama")
                savedSocketId = storedSocketId;
                localStorage.setItem(
                    accessKey,
                    JSON.stringify({
                        socket_id: storedSocketId,
                        name: userData?.name,
                        phone_number: userData?.phone_number,
                    })
                );
                localStorage.removeItem("socket_id");
            }

            if (!storedAccessKeyData2) {
                localStorage.setItem(
                    accessKey,
                    JSON.stringify({
                        socket_id: savedSocketId,
                        name: userData?.name,
                        phone_number: userData?.phone_number,
                    })
                );
            }

            socket.emit("storeClientInfo", userData, accessKey);

            if (storedConversationId) {
                socket.emit("request-history", storedConversationId);
            }
        });

        socket.on("set-user-data", (userData) => {
            const storedAccessKeyData4 = JSON.parse(
                localStorage.getItem(accessKey)
            );

            localStorage.setItem(
                accessKey,
                JSON.stringify({
                    ...storedAccessKeyData4,
                    name: userData?.name,
                    phone_number: userData?.phone,
                })
            );
        });

        socket.on("set-conversation", (convId, idcontact) => {
            // console.log("Received conversation data:", { convId, idcontact });
            const storedAccessKeyData3 = JSON.parse(
                localStorage.getItem(accessKey)
            );

            conversationId = convId;
            contactId = idcontact; 
            localStorage.setItem(
                accessKey,
                JSON.stringify({
                    ...storedAccessKeyData3,
                    conversation_id: convId,
                    idcontact: idcontact,
                })
            );

            localStorage.removeItem("conversation_id");
            localStorage.removeItem("idcontact");

            // if (chatMessages.children.length === 0) {
            //   const loadingIndicator = document.createElement("div");
            //   loadingIndicator.className = "loading-indicator";

            //   const spinner = document.createElement("div");
            //   spinner.className = "loading-spinner";
            //   loadingIndicator.appendChild(spinner);

            //   const loadingText = document.createElement("div");
            //   loadingText.textContent = "Loading...";
            //   loadingIndicator.appendChild(loadingText);
            //   chatMessages.appendChild(loadingIndicator);
            // }

            socket.emit("request-history", convId);
        });

        socket.on("history", (messages) => {
            // console.log("Received history:", messages);
            isHistoryFetched = true;
            const loadingIndicator =
                document.querySelector(".loading-indicator");
            if (loadingIndicator) {
                loadingIndicator.remove();
            }
            messages?.data.reverse().forEach((message) => {
                if (!displayedMessageIds.has(message.id)) {
                    displayMessage(message);
                    displayedMessageIds.add(message.id);
                }
            });
        });

        socket.on("agent-typing", () => {
            // Show the typing indicator when AI is processing
            typingIndicator.style.display = "inline";
        });

        socket.on("agent-stop-typing", () => {
            // Hide the typing indicator when AI stops processing
            typingIndicator.style.display = "none";
        });

        socket.on("receive-message", (message) => {
            // console.log(message, "message received");
            // if (!isHistoryFetched) {
            //   return;
            // }
            displayMessage(message);
            if (message?.sent_by !== contactId) {
              //  showNotification();
            }
        });
    };

    function handleUIBasedOnProfile(isProfileRequired) {
        let name, phone_number, savedSocketId;

        const storedAccessKeyData1 = JSON.parse(
            localStorage.getItem(accessKey)
        );
        if (storedAccessKeyData1) {
            (name = storedAccessKeyData1.name),
                (phone_number = storedAccessKeyData1.phone_number);
            savedSocketId = storedAccessKeyData1.socket_id;
        }

        const storedUserData1 = JSON.parse(
            localStorage.getItem("chats_user_data")
        );
        if (storedUserData1) {
            (name = storedUserData1.name),
                (phone_number = storedUserData1.phone_number);
        }

        if (isProfileRequired == false) {
            userInfoForm.classList.remove("visible");
            chatMessagesContainer.style.display = "block";
            document
                .getElementById("chat-input-container")
                .classList.add("visible");

            connectSocket();
        } else {
            if (storedAccessKeyData1 || storedUserData1) {
                userInfoForm.classList.remove("visible");
                chatMessagesContainer.style.display = "block";
                document
                    .getElementById("chat-input-container")
                    .classList.add("visible");
                connectSocket({
                    name,
                    phone_number,
                });
                localStorage.removeItem("chats_user_data");
            } else {
                // console.log("else ya")
                userInfoForm.classList.add("visible");
                chatMessagesContainer.style.display = "none";
                document
                    .getElementById("chat-input-container")
                    .classList.remove("visible");
            }
        }
    }

    // New function to fetch inbox info
    const fetchInformation = async () => {
        try {
            const response = await fetch(
                `${webServer}/chats/information?token=${accessKey}`,
                {
                    method: "GET",
                    credentials: "include",
                    headers: {
                        Accept: "application/json",
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                }
            );

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            inboxData = data;

            if (chatPopup) {
                try {
                    updateProfile(chatPopup, inboxData);
                    updatePopupBubble(chatBubble, inboxData);
                    updateFAQs(inboxData?.detail?.faq);
                } catch (error) {
                    console.log("error updating UI:", error);
                }
            }

            // Check if profile is required
            const isProfileRequired =
                inboxData?.chats_data?.is_profile_required;

            // Handle the UI based on the profile requirement
            handleUIBasedOnProfile(isProfileRequired);
        } catch (error) {
            console.error("Error fetching inbox info:", error);
        }
    };

    // Call fetchInformation immediately when widget loads
    fetchInformation();

    // Add this function to format phone number
    function formatPhoneNumber(phone) {
        // Remove any non-digit characters
        let cleaned = phone.replace(/\D/g, "");

        // Add 62 prefix if starts with 0
        if (cleaned.startsWith("0")) {
            cleaned = "62" + cleaned.substring(1);
        }

        return cleaned;
    }

    // Update the form submission handler
    contactForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const phoneInput = document.getElementById("user-phone");
        const phone = formatPhoneNumber(phoneInput.value);

        // Basic validation
        if (phone.length < 7) {
            phoneInput.setCustomValidity("Please enter a valid phone number");
            phoneInput.reportValidity();
            return;
        }

        phoneInput.setCustomValidity(""); // Clear any validation message

        const userData = {
            name: document.getElementById("user-name").value,
            phone_number: phone,
        };

        // localStorage.setItem('chats_user_data', JSON.stringify(userData));

        if (userData?.name && userData?.phone_number) {
            connectSocket(userData);
        }

        if (savedSocketId) {
            socket.emit("storeClientInfo", userData, accessKey);
        }

        // Show chat interface
        userInfoForm.classList.remove("visible");
        chatMessagesContainer.style.display = "block";
        document
            .getElementById("chat-input-container")
            .classList.add("visible"); // Show input area
        chatInput.focus();
    });

    // Add input validation for phone number
    document
        .getElementById("user-phone")
        .addEventListener("input", function (e) {
            let value = e.target.value.replace(/\D/g, "");

            // Remove leading zero if present
            if (value.startsWith("0")) {
                value = value.substring(1);
            }

            // Format with spaces for readability (but don't add spaces at the end)
            if (value.length > 0) {
                const groups = value.match(new RegExp(".{1,4}", "g")) || [
                    value,
                ];
                value = groups.join(" ").trim();
            }

            e.target.value = value;
        });

    // Update the clear function to clear all stored data
    window.clearchatsData = () => {
        localStorage.removeItem("chats_user_data");
        localStorage.removeItem("socket_id");
        localStorage.removeItem("conversation_id");
        localStorage.removeItem("idcontact");
    };

    const disconnectSocket = () => {
        if (socket) {
            socket.disconnect();
            isSocketConnected = false;
        }
    };

    // Update the chat bubble click handler to handle the initial connection
    chatBubble.addEventListener("click", () => {
        // console.log("clicked", {inboxData});
        togglePopup();

        const chatIcon = document.getElementById("chat-icon");
        const profileImage = chatBubble.querySelector("#profile-image");

        if (chatPopup.classList.contains("visible")) {
            // Change to 'X' icon when popup is visible
            chatIcon.style.display = "block";
            chatIcon.innerHTML = `
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 6l12 12M6 18L18 6"
        />
      `;
            profileImage.style.display = "none";
        } else {
            // Change back to chat bubble icon or show profile image
            if (inboxData?.img_url) {
                profileImage.src = inboxData.img_url;
                profileImage.style.display = "block";
                chatIcon.style.display = "none";
            } else {
                chatIcon.innerHTML = `
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
          />
        `;
                profileImage.style.display = "none";
                chatIcon.style.display = "block";
            }
        }

        chatBubble.classList.toggle("active");
        sendMessageToParent();
    });

    closePopupButton.addEventListener("click", () => {
        chatBubble.click(); // This will trigger the chat bubble click event to change the icon
    });

    function updateProfile(chatPopup, inboxData) {
        const profileImage = chatPopup.querySelector("#profile-image");
        const inboxName = chatPopup.querySelector("#inbox-name");
 
        if (profileImage && inboxData?.img_url) {
            profileImage.src = inboxData.img_url;
        }

        if (inboxName) {
            inboxName.innerHTML = inboxData.name;
        }
    }

    function updatePopupBubble(chatBubble, inboxData) {
        const profileImage = chatBubble.querySelector("#profile-image");
        const chatIcon = chatBubble.querySelector("#chat-icon");
        const inboxName = chatBubble.querySelector("#inbox-name");

        if (profileImage && chatIcon) {
            if (inboxData?.img_url) {
                profileImage.src = inboxData.img_url;
                profileImage.style.display = "block";
                chatIcon.style.display = "none";
            } else {
                profileImage.style.display = "none";
                chatIcon.style.display = "block";
                chatIcon.innerHTML = `
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
          />
        `;
            }
        }

        if (inboxName) {
            inboxName.innerHTML = inboxData.name;
        }
    }

    function updateFAQs(faqs) {
        const faqContainer = document.getElementById("inbox-faq");

        if (!faqs || !faqs.length) {
            faqContainer.classList.remove("visible");
            return;
        }

        if (faqContainer) {
            faqContainer.innerHTML = ""; // Clear existing FAQ buttons

            // Create new FAQ buttons
            faqs.forEach((faq) => {
                const button = document.createElement("button");
                button.className =
                    "block shrink-0 px-4 py-2 bg-white border border-gray-200 rounded-md hover:bg-gray-100 cursor-pointer";
                button.textContent = faq;
                faqContainer.appendChild(button);
            });

            faqContainer.classList.add("visible");
        }
    }

    inboxFaq.addEventListener("click", function (event) {
        if (event.target.tagName === "BUTTON") {
            const message = event.target.textContent;
            chatInput.value = message;
            chatSubmit.click();
        }
    });

    document
        .getElementById("upload-button")
        .addEventListener("click", function () {
            const fileInput = document.createElement("input");
            fileInput.type = "file";
            fileInput.accept = "image/*"; // Specify accepted file types (optional)

            // Add event listener for file selection
            fileInput.addEventListener("change", function (event) {
                selectedFile = event.target.files[0];

                // Handle the selected file
                if (selectedFile) {
                    // Example: Display the selected image
                    const imagePreview = document.createElement("img");
                    imagePreview.src = URL.createObjectURL(selectedFile);
                    imagePreview.alt = "Selected image";
                    imagePreview.style.maxWidth = "40px"; // Adjust the max width as needed
                    imagePreview.style.borderRadius = "5px";

                    // Create a container for the image preview
                    const imageContainer = document.createElement("div");
                    imageContainer.appendChild(imagePreview);

                    // Append the image container next to the chat input
                    chatInput.parentNode.insertBefore(
                        imageContainer,
                        chatInput.nextSibling
                    );
                }
            });

            // Trigger the file input
            fileInput.click();
        });

    chatSubmit.addEventListener("click", function () {
        const message = chatInput.value.trim();

        const storedAccessKeyData6 = JSON.parse(
            localStorage.getItem(accessKey)
        ); 

         

        //reset the chatinput height
        chatInput.style.height = "auto";
        // Check if there is a message or an attached file
        if (message || selectedFile) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
            chatInput.value = "";

            const messageData = {
                conversationId:
                    conversationId || storedAccessKeyData6.conversation_id,
                contactId: contactId || storedAccessKeyData6?.idcontact,
                savedSocketId,
                name: storedAccessKeyData6?.name,
                phone_number: storedAccessKeyData6?.phone_number,
            };

            // Send the message and image data to the server
            if (selectedFile) {
                const fileSize = selectedFile?.size; // in bytes
                const mimetype = selectedFile?.type;
                const fileName = selectedFile?.name;
                const chunkThreshold = 5 * 1024 * 1024; // 5 MB
                const reader = new FileReader();

                // Determine max size based on file type
                //  - Images: max 5 MB
                //  - Video/Other: max 16 MB
                let maxSize;
                if (mimetype.startsWith("image/")) {
                    maxSize = 5 * 1024 * 1024; // 5 MB
                } else {
                    maxSize = 16 * 1024 * 1024; // 16 MB
                }

                if (fileSize > maxSize) {
                    alert(
                        `File size must not exceed ${
                            maxSize / (1024 * 1024)
                        } MB for this file type.`
                    );
                    // Clear selectedFile and preview
                    selectedFile = null;
                    const filePreview = chatInput.nextSibling;
                    if (filePreview) filePreview.remove();
                    return;
                }

                // If file is above 5 MB, let's chunk it
                if (fileSize > chunkThreshold) {
                    sendFileInChunks(
                        selectedFile,
                        message,
                        messageData,
                        inboxData
                    );
                } else {
                    // Send in one go (no chunking)
                    const reader = new FileReader(); 
                    
                    reader.onloadend = function () {
                        const arrayBuffer = reader.result;
                       
                        socket.emit(
                            "send-message",
                            message,
                            messageData,
                            {
                                mimetype,
                                fileName,
                                chunk: arrayBuffer,
                                fileSize,
                                chunkIndex: 0, // single chunk
                                totalChunks: 1,
                            },
                            inboxData
                        );
                    };
                    reader.readAsArrayBuffer(selectedFile);
                }

                // Clear selectedFile and remove preview from UI
                selectedFile = null;
                const imageContainer = chatInput.nextSibling;
                if (imageContainer) {
                    imageContainer.remove();
                }
            } else {
                socket.emit(
                    "send-message",
                    message,
                    messageData,
                    null,
                    inboxData
                );
            }
        }
    });

    function sendFileInChunks(file, message, messageData, inboxData) {
        const chunkSize = 5 * 1024 * 1024; // 5 MB
        const fileSize = file.size;
        const mimetype = file.type;
        const fileName = file.name;
        const totalChunks = Math.ceil(fileSize / chunkSize);

        let offset = 0;
        let chunkIndex = 0;

        function readNextSlice() {
            if (offset >= fileSize) {
                return; // all chunks read
            }

            const slice = file.slice(offset, offset + chunkSize);
            const reader = new FileReader();

            reader.onload = function (e) {
                const arrayBuffer = e.target.result;

                // Emit each chunk with chunkIndex, totalChunks, etc.
                socket.emit(
                    "send-message",
                    message,
                    messageData,
                    {
                        mimetype,
                        fileName,
                        fileSize,
                        chunkIndex,
                        totalChunks,
                        chunk: arrayBuffer,
                    },
                    inboxData
                );

                // Move to the next chunk
                offset += chunkSize;
                chunkIndex++;
                readNextSlice();
            };

            reader.onerror = function (error) {
                console.error("Error reading file chunk: ", error);
                // handle error if needed
            };

            reader.readAsArrayBuffer(slice);
        }

        // Start reading chunks
        readNextSlice();
    }

    chatInput.addEventListener("keydown", function (event) {
        if (event.key === "Enter" && !event.shiftKey) {
            event.preventDefault();
            chatSubmit.click();
            // reset the textarea height
            this.style.height = "auto";
        }
    });

    chatInput.addEventListener(
        "input",
        function () {
            this.style.height = "auto";
            this.style.height = this.scrollHeight + "px";
        },
        false
    );

    // Add mobile detection function
    function isMobile() {
        return window.innerWidth < 768;
    }

    // Handle mobile view
    function handleMobileView(forceMobile = null) {
        // Remove existing class first
        chatPopup.classList.remove("mobile");

        // If forceMobile is provided, use that value, otherwise check window width
        const mobile = forceMobile !== null ? forceMobile : isMobile();

        if (mobile) {
            chatPopup.classList.add("mobile");
        }
    }

    // Don't call handleMobileView immediately - wait for parent window message

    // Listen for messages from parent window
    window.addEventListener("message", function (event) {
        if (event.data === "enter-mobile") {
            handleMobileView(true);
        } else if (event.data === "exit-mobile") {
            handleMobileView(false);
        }
    });

    // Update the togglePopup function
    function togglePopup() {
        chatPopup.classList.toggle("visible");
        if (chatPopup.classList.contains("visible")) {
            chatInput.focus();
        }
    }

    function showNotification() {
        const sound = new Audio("./notification.mp3");
        // Check if sound is already playing
        if (!sound.paused) {
            return; // Sound is already playing, so return without playing it again
        }

        sound.play();
    }

    function displayMessage(message) {
        let messagesList = document.getElementById("chat-messages");

        // Check if the last message was from the same sender
        let lastMessageDiv = messagesList.lastElementChild;
        let sameSender =
            lastMessageDiv &&
            lastMessageDiv.getAttribute("data-sender") === message.sent_by;

        // Create a container for the message bubble and timestamp
        let messageContainer = document.createElement("div");
        messageContainer.setAttribute("data-sender", message.sent_by);

        let bubbleClass =
            message.sent_by === "system"
                ? ""
                : message.sent_by === contactId
                ? "outgoing-bubble"
                : "incoming-bubble";
        let flexClass =
            message.sent_by === "system"
                ? "justify-center"
                : message.sent_by === contactId
                ? "justify-end"
                : "justify-start";

        // Create the message bubble
        let messageBubble = document.createElement("div");
        messageBubble.className = `flex mb-3 ${flexClass}`;
        messageBubble.innerHTML = getMessageContent(message, bubbleClass);

        // Create and append the timestamp
        let timestampDiv = document.createElement("div");
        timestampDiv.className = `timestamp flex ${flexClass} -mt-2 mx-1`;
        timestampDiv.textContent = convertTimestampToHHMM(message?.created_at);
        messageContainer.appendChild(messageBubble);
        messageContainer.appendChild(timestampDiv);

        // Append the new message container
        messagesList.appendChild(messageContainer);

        // If the last message was from the same sender, remove its timestamp
        if (sameSender && lastMessageDiv) {
            let lastTimestamp = lastMessageDiv.querySelector(".timestamp");
            if (lastTimestamp) {
                lastMessageDiv.removeChild(lastTimestamp);
            }
        }

        // Scroll to the bottom
        messagesList.scrollTop = messagesList.scrollHeight;
    }

    function getMessageContent(message, bubbleClass) {
        // Helper function to convert URLs to clickable links
        // function linkifyText(text) {
        //   const urlRegex = /(https?:\/\/[^\s]+)/g;
        //   return text.replace(urlRegex, url => `<a href="${url}" target="_blank" class="text-blue-600 underline">${url}</a>`);
        // }

        function linkifyText(text) {
            const urlRegex = /(https?:\/\/[^\s]+)/g;
            return text.replace(urlRegex, (url) => {
                // Check if URL ends with any punctuation like . , ? !
                const hasTrailingDot = /\.$/.test(url);
                let cleanUrl = url.replace(/[.,!?]+$/, "");

                return `<a href="${cleanUrl}" target="_blank" class="text-blue-600 underline">${cleanUrl}</a>`;
            });
        }

        if (
            message?.media_url &&
            (message?.media_type === "image" || message?.media_type === "video")
        ) {
            return `
         <div class="message-bubble bg-gray-200 text-black rounded-lg py-2 px-4 max-w-[70%] ${bubbleClass}">
          ${
              message?.media_type === "image"
                  ? `<img src="${message.media_url}" alt="Sent image" style="max-width: 200px; border-radius: 5px;">`
                  : `<video controls style="max-width: 200px; border-radius: 5px;">
                  <source src="${message.media_url}" type="video/mp4">
                  Your browser does not support the video tag.
                </video>`
          }
          <p class="mt-2">${
              message?.message ? linkifyText(message.message.trim()) : ""
          }</p>
        </div>
      `;
        } else if (message?.media_url && message?.media_type === "document") {
            const fileName = message.media_url.split("/").pop(); // Extract filename from URL

            return `
       <div class="message-bubble bg-gray-200 text-black rounded-lg py-2 px-4 max-w-[70%] ${bubbleClass}">
          <a href="${
              message.media_url
          }" target="_blank" class="flex items-center space-x-2 text-blue-600">
            <img src="./icons8-document.svg" alt="Document" class="w-6 h-6">
            <span class="truncate max-w-[50%] overflow-hidden whitespace-nowrap text-ellipsis flex-1">
              ${fileName || "Document"}
            </span>
          </a>
          <p class="mt-2">${
              message?.message ? linkifyText(message.message.trim()) : ""
          }</p>
        </div>
      `;
        } else {
            return `
        <div class="message-bubble bg-gray-200 text-black rounded-lg py-2 px-2 max-w-[70%] ${bubbleClass}">
          <p class="whitespace-pre-line">${
              message?.message ? linkifyText(message.message.trim()) : ""
          }</p>
        </div>
      `;
        }
    }

    function sendMessageToParent() {
        window.parent.postMessage({ action: "toggleChat" }, "*"); // '*' can be replaced with the exact parent origin for added security
    }

    // Inside your iframe script
    window.addEventListener("message", function (event) {
        // Optionally, check event.origin here for security
        if (event.data === "enter-mobile") {
            // Code to handle entering mobile view
            chatPopup.classList.add("mobile");
        } else if (event.data === "exit-mobile") {
            // Code to handle exiting mobile view
            chatPopup.classList.remove("mobile");
        }
    });

    function convertTimestampToHHMM(timestamp) {
        // Convert the timestamp string to a Date object
        const date = new Date(timestamp);

        // Check if the date is valid
        if (isNaN(date.getTime())) {
            return ""; // Return empty string if the date is not valid
        }

        // Get hours and minutes
        const hours = date.getHours().toString().padStart(2, "0");
        const minutes = date.getMinutes().toString().padStart(2, "0");

        // Format the result as HH:MM
        const formattedTime = `${hours}:${minutes}`;

        return formattedTime;
    }
});
