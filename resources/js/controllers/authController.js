import router from "../router/index.js";
import { toast } from "vue3-toastify";
import Echo from "laravel-echo";

export const authController = {
    async handleLogin(credentials) {
        const loginStatus = toast.loading("Please wait...", {
            position: toast.POSITION.TOP_RIGHT,
        });

        await axios.get("sanctum/csrf-cookie");

        await axios
            .post("api/login", credentials)
            .then((response) => {
                toast.update(loginStatus, {
                    render: () => {
                        return "Redirecting...";
                    },
                    type: toast.TYPE.SUCCESS,
                    isLoading: false,
                    autoClose: 3000,
                });
                setTimeout(() => {
                    localStorage.setItem("token", response.data?.token);
                    this.startLaravelEcho();
                    router.push("/");
                }, 3000);
            })
            .catch((error) => {
                toast.update(loginStatus, {
                    render: () => {
                        return (
                            "Error - " + error.response.data.message ??
                            error.response.status
                        );
                    },
                    type: toast.TYPE.ERROR,
                    isLoading: false,
                    autoClose: 5000,
                });
            });
    },
    async handleLogout() {
        try {
            const token = this.getToken();
            await axios.post("/api/logout", null, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });
            this.removeAuthentication();
            router.push("/login");
        } catch (error) {
            if (
                error.response?.status === 401 ||
                error.response?.status === 419
            ) {
                this.removeAuthentication();
                router.push("/login");
            }
        }
    },
    async getAuthUser() {
        try {
            const token = this.getToken();
            const data = await axios.get("/api/user", {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });
            return data?.data;
        } catch (error) {
            if (
                error.response?.status === 401 ||
                error.response?.status === 419
            ) {
                this.removeAuthentication();
            }
        }

        return false;
    },

    removeAuthentication() {
        localStorage.removeItem("token");
    },

    getToken() {
        return localStorage.getItem("token");
    },

    isLoggedIn() {
        const token = localStorage.getItem("token");
        if (!token) {
            return false;
        }

        const userData = this.getAuthUser();

        if (!userData) {
            return false;
        }

        return true;
    },

    startLaravelEcho() {
        window.Echo = new Echo({
            broadcaster: "pusher",
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            wsHost:
                import.meta.env.VITE_PUSHER_HOST ??
                `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
            wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
            wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
            forceTLS:
                (import.meta.env.VITE_PUSHER_SCHEME ?? "https") === "https",
            enabledTransports: ["ws", "wss"],
            authEndpoint: "api/broadcasting/auth",
            auth: {
                headers: {
                    Authorization: "Bearer " + this.getToken(),
                },
            },
        });
    },
};
