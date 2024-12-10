import LaraPrime, { LaraPrimeConfig } from "@/LaraPrime";
import { AxiosInstance } from "axios";

declare global {
    interface Window {
        axios: AxiosInstance;
        createLaraPrimeApp: (config: LaraPrimeConfig) => LaraPrime;
        LaraPrime: LaraPrime;
    }
    declare var LaraPrime: LaraPrime;
}
