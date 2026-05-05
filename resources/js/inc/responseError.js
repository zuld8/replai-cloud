export const errorHandlerMixin = {
    methods: {
        $handleErrorResponse(error) {
            console.error("Error Response:", error); // Log detail untuk developer

            if (error.response) {
                const status = error.response.status;
                const msg = error.response.data?.message || "Unknown error occurred.";

                if ([419, 422, 401, 409, 404, 403].includes(status)) {
                    this.$showToast(msg, "error", 3000);
                } else {
                    this.$showToast(
                        "A system error occurred, check your WhatsApp and internet connection.",
                        "error",
                        3000
                    );
                }
            } else {
                this.$showToast(
                    "A system error occurred, check your WhatsApp and internet connection.",
                    "error",
                    3000
                );
            }
        },
        $handleSuccessResponse(message) {
            this.$showToast(message, "success", 3000);
        },
    },
};
