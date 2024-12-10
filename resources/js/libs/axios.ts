import { VisitCallback } from "@/types";
import axios from "axios";
import type Emitter from "./emitter";
const getAxios = (
    emitter: Emitter,
    redirectCallback?: () => void,
    visitCallback?: VisitCallback
) => {
    const instance = axios.create();
    instance.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

    instance.interceptors.response.use(
        (response) => response,
        (error) => {
            if (axios.isCancel(error)) {
                return Promise.reject(error);
            }

            const response = error.response;

            const {
                status,
                data: { redirect },
            } = response;

            // Handle Server Errors
            if (status >= 500) {
                emitter.$emit("error", error.response.data.message);
            }

            // Handle Session Timeouts
            if (status === 401) {
                // If response from Inertia is a redirect, then redirect
                if (redirect !== void 0 && redirect !== null) {
                    window.location.href = redirect;
                    return;
                }

                redirectCallback && redirectCallback();
            }

            // Handle Forbidden
            if (status === 403) {
                visitCallback && visitCallback("/403");
            }

            // Handle Token Mismatch
            if (status === 419) {
                emitter.$emit("tokenMismatch");
            }

            return Promise.reject(error);
        }
    );

    return instance;
};

export default getAxios;
