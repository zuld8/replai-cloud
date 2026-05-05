(function () {
    window.mychat = window.mychat || {};
    let chatPopupOpen = false;

    function isMobile() {
        return window.innerWidth < 768;
    }

    function updateIframeStyles() {
        const mobile = isMobile();

        const chatBubbleHeight = chatPopupOpen ? "100%" : "84px";
        const chatBubbleWidth = chatPopupOpen ? "422px" : "84px";
        const iframe2Styles = {
            position: "fixed",
            "z-index": "2147483639",
            overflow: "hidden",
            border: "0px",
            "background-color": "transparent",
            "pointer-events": "all",
            opacity: "1",
            width: mobile ? (chatPopupOpen ? "100%" : "84px") : chatBubbleWidth,
            height: mobile
                ? chatPopupOpen
                    ? "100%"
                    : "84px"
                : chatBubbleHeight,
            bottom: "0",
            right: "0",
            "max-width": mobile ? "100%" : "80%",
            "max-height": mobile ? "100%" : "90%",
        };

        const iframe1Styles = {
            "pointer-events": "all",
            background: "none",
            border: "0px",
            position: "absolute",
            inset: "0px",
            width: "100%",
            height: "100%",
            margin: "0px",
            padding: "0px",
            "min-height": "0px",
        };

        Object.assign(iframe2.style, iframe2Styles);
        Object.assign(iframe1.style, iframe1Styles);
    }

    const iframe2 = document.createElement("div");
    const appUrl = window.mychat.appUrl || '';
    iframe2.id = "iframe2-chat-container";

    const iframe1 = document.createElement("iframe");
    iframe1.src = appUrl + "/chats?chat=" + window.mychat.tokenKey;
    iframe1.id = "iframe1";
    iframe1.allow = "autoplay; camera; microphone";

    iframe2.appendChild(iframe1);
    document.querySelector("body").appendChild(iframe2);

    iframe1.addEventListener("load", sendMobileState);

    window.addEventListener("message", function (event) {
        // Optionally check event.origin here if you want to verify the source

        if (event.data.action === "toggleChat") {
            toggleChatPopup();
        }
    });

    let isCurrentlyMobile = isMobile();

    function sendMobileState() {
        const mobile = isMobile();
        const message = mobile ? "enter-mobile" : "exit-mobile";
        iframe1.contentWindow.postMessage(message, "*");
    }

    // Debounce the resize handler
    let resizeTimeout;
    window.addEventListener("resize", function () {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            const mobile = isMobile();
            if (mobile !== isCurrentlyMobile) {
                sendMobileState();
                isCurrentlyMobile = mobile;
            }
            updateIframeStyles();
        }, 100);
    });

    function toggleChatPopup() {
        // Code to toggle the chat popup
        chatPopupOpen = !chatPopupOpen;
        updateIframeStyles(); // Assuming updateIframeStyles is the function that adjusts the chat window size
    }
    updateIframeStyles();
    window.addEventListener("resize", updateIframeStyles);
})();
