import axios from "axios";
import router from "@/router/index.js";

const axiosClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  withCredentials: true,
  withXSRFToken: true
})

// axiosClient.interceptors.request.use(config => {
//   config.headers.Authorization = `Bearer ${localStorage.getItem('token')}`
// })

axiosClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      // Redirect to login page
      if (router.currentRoute.value.name !== "LoginView") {
        router.push({ name: "LoginView" });
      }
    }
    throw error;
  }
);

export default axiosClient