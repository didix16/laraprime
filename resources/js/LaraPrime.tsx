import AppLayout from "@/layouts/AppLayout";
import getAxios from "@/libs/axios";
import ComponentRegistry from "@/libs/component-registry";
import Emitter from "@/libs/emitter";
import LaraForm from "@/libs/form";
import SoundManager from "@/libs/sound";
import {
    ConfirmDialogOptions,
    InferArrayType,
    LaraPrimeUser,
    PageComponent,
    VisitCallback,
} from "@/types";
import { url } from "@/utils";
import { createInertiaApp, router } from "@inertiajs/react";
import type { AxiosInstance, AxiosRequestConfig } from "axios";
import { PrimeReactProvider } from "primereact/api";
import { confirmDialog, ConfirmDialog } from "primereact/confirmdialog";
import { Toast } from "primereact/toast";
import { createRoot } from "react-dom/client";

export type LaraPrimeConfig = Record<string, any>;

export default class LaraPrime extends Emitter {
    protected appConfig: LaraPrimeConfig;
    protected pages: Record<string, PageComponent> = {};
    protected $toast: Toast | null = null;
    protected soundManager: SoundManager = new SoundManager();
    protected componentRegistry: ComponentRegistry = new ComponentRegistry();
    protected pageProps: Record<string, any> = {};

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
        const self = this;

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
                self.pageProps = props.initialPage.props;
                createRoot(el).render(
                    <PrimeReactProvider
                        value={{
                            ripple: true,
                            locale: appLocale,
                        }}
                    >
                        <Toast ref={(el) => (self.$toast = el)} />
                        <ConfirmDialog />
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
     * Show a info toast message to the user
     * @param message The info message to show
     * @returns The LaraPrime instance
     */
    public info(message: string) {
        this.$toast?.show({
            severity: "info",
            summary: "Info",
            detail: message,
            life: 3000,
        });
        return this;
    }

    /**
     * Show an error toast message to the user
     * @param message The error message to show
     * @returns The LaraPrime instance
     */
    public error(message: string) {
        this.$toast?.show({
            severity: "error",
            summary: "Error",
            detail: message,
            life: 3000,
        });
        return this;
    }

    /**
     * Show a success toast message to the user
     * @param message The success message to show
     * @returns The LaraPrime instance
     */
    public success(message: string) {
        this.$toast?.show({
            severity: "success",
            summary: "Success",
            detail: message,
            life: 3000,
        });
        return this;
    }

    /**
     * Show a warning toast message to the user
     * @param message The warning message to show
     * @returns The LaraPrime instance
     */
    public warn(message: string) {
        this.$toast?.show({
            severity: "warn",
            summary: "Warning",
            detail: message,
            life: 3000,
        });
        return this;
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

    public async confirm({ message, title }: ConfirmDialogOptions) {
        return new Promise((resolve, reject) => {
            confirmDialog({
                message,
                defaultFocus: "accept",
                position: "top",
                header: title || "Confirm",
                closable: false,
                accept: () => {
                    this.$emit("confirmDialog");
                    resolve(true);
                },
                reject: () => {
                    this.$emit("rejectDialog");
                    resolve(false);
                },
            });
            this.soundManager.playSound("confirm-show");
        });
    }

    /**
     * Try to logout the user from the application.
     *
     * @param customLogoutPath - The custom path to logout the user from the application.
     * @returns A promise with the redirect URL after logout.
     */
    public async logout(customLogoutPath?: string) {
        const response = await this.request().post(
            !this.config("withAuthentication") && customLogoutPath
                ? customLogoutPath
                : this.url("/logout")
        );

        return response?.data?.redirect || null;
    }

    /**
     * Get the current user object
     * @returns The current user object or null if not authenticated
     */
    public currentUser(): LaraPrimeUser | null {
        return this.pageProps.currentUser ?? null;
    }

    /**
     * Register a component with a name to be used later on the application
     * @param name
     * @param component
     * @returns
     */
    public addComponent(name: string, component: React.ComponentType<any>) {
        this.componentRegistry.register(name, component);
        return this;
    }

    /**
     * Get a component registered with the given name. Returns null if not found
     * @param name The name of the component to get
     * @returns The component registered with the given name or null if not found
     */
    public component(name: string): React.ComponentType<any> | null {
        return this.componentRegistry.get(name);
    }
}
