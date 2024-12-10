import AppLayout from "@/layouts/AppLayout";
import getAxios from "@/libs/axios";
import Emitter from "@/libs/emitter";
import { InferArrayType, PageComponent, VisitCallback } from "@/types";
import { url } from "@/utils";
import { createInertiaApp, router } from "@inertiajs/react";
import type { AxiosInstance, AxiosRequestConfig } from "axios";
import { PrimeReactProvider } from "primereact/api";
import { createRoot } from "react-dom/client";
import LaraForm from "./libs/form";

export type LaraPrimeConfig = Record<string, any>;

export default class LaraPrime extends Emitter {
    protected appConfig: LaraPrimeConfig;
    protected pages: Record<string, PageComponent> = {};

    constructor(config: LaraPrimeConfig) {
        super();
        this.appConfig = config;

        // Fix keys. Strip the './pages/' prefix and '.tsx' suffix
        this.pages = Object.fromEntries(
            Object.entries(
                import.meta.glob("./pages/**/*.tsx", {
                    eager: true,
                    import: "default",
                })
            ).map(([key, value]) => [
                key.replace("./pages/", "").replace(".tsx", ""),
                value,
            ])
        ) as Record<string, PageComponent>;

        //inject <link> tag for theme css file in the head of the document
        const theme = this.config("theme");
        if (theme) {
            const link = document.createElement("link");
            link.id = "theme-link";
            link.rel = "stylesheet";
            link.href = `/vendor/laraprime/themes/${theme}/theme.css`;
            document.head.appendChild(link);
        }
    }

    public async bootstrap() {
        this.log("bootstrapping...");

        const appName = this.config("appName");
        const theme = this.config("theme");
        const appLocale = this.config("locale");

        await createInertiaApp({
            title: (title) => (!title ? appName : `${appName} - ${title}`),
            resolve: (name: string) => {
                // import the page component or if not found, return the 404 page
                const page = this.pages[name] ?? this.pages["Error404"];
                page.layout =
                    page.layout ||
                    ((page: PageComponent) => (
                        <AppLayout theme={theme} children={page} />
                    ));
                return page;
            },
            setup({ el, App, props }) {
                createRoot(el).render(
                    <PrimeReactProvider
                        value={{
                            ripple: true,
                            locale: appLocale,
                        }}
                    >
                        <App {...props} />
                    </PrimeReactProvider>
                );
            },
        });
    }

    public init() {
        this.log("initialized");
    }

    public log(
        message: string,
        type: "log" | "warn" | "info" | "error" = "log"
    ) {
        console[type](`[LaraPrime] ${message}`);
    }

    public config(key: string) {
        return this.appConfig[key];
    }

    /**
     * Get the URL from the base LaraPrime prefix
     * @param path
     * @param params
     * @returns
     */
    public url(
        path: string,
        params: InferArrayType<
            ConstructorParameters<typeof URLSearchParams>
        > = {}
    ) {
        return url(
            this.config("baseUrl"),
            path === "/" ? this.config("initialPath") : path,
            params
        );
    }

    /**
     * Return a LaraForm object configured with LaraPrime's preconfigured axios instance.
     */
    public form(data: Record<string, any>) {
        return new LaraForm(data, {
            httpLib: this.request(),
        });
    }

    /**
     * Return an axios instance configured to make requests to LaraPrime's API
     * and handle certain response codes.
     */
    public request(): AxiosInstance;
    public request(options?: AxiosRequestConfig) {
        const axios = getAxios(this, this.redirectToLogin, this.visit);

        if (options !== void 0) {
            return axios(options);
        }

        return axios;
    }

    public redirectToLogin() {
        const url =
            !this.config("withAuthentication") && this.config("customLoginPath")
                ? this.config("customLoginPath")
                : this.url("/login");

        this.visit({ url, remote: true });
    }

    /**
     * Visit page using Inertia's router.visit or window.location for remote.
     */
    public visit(
        ...args: [
            Parameters<VisitCallback>[0] | { url: string; remote: boolean },
            Parameters<VisitCallback>[1]?
        ]
    ) {
        const arg0 = args[0];
        if (
            arg0 &&
            typeof arg0 === "object" &&
            !(arg0 instanceof URL) &&
            arg0.url &&
            arg0.remote
        ) {
            window.location.href = arg0.url;
            return;
        }
        router.visit(...(args as Parameters<VisitCallback>));
    }
}
