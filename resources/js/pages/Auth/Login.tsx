//import { PrimeReactContext } from "primereact/api";
//import { useContext } from "react";

import useLaraForm from "@/hooks/useLaraForm";
import AuthLayout from "@/layouts/AuthLayout";
import { PageComponent } from "@/types";
import { classNames } from "@/utils";
import { Head } from "@inertiajs/react";
import { AxiosError } from "axios";
import { BlockUI } from "primereact/blockui";
import { Checkbox } from "primereact/checkbox";
import { ProgressSpinner } from "primereact/progressspinner";
import { FormEvent } from "react";

const Login: PageComponent = () => {
    //const { changeTheme } = useContext(PrimeReactContext);

    // if(changeTheme){
    //     changeTheme(window.LaraPrime.config('theme'), 'lara-dark-cyan', 'theme-link');
    // }

    const { form } = useLaraForm({
        email: "",
        password: "",
        remember: false,
    });

    const handleSubmit = (e: FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        if (form.processing) {
            return;
        }
        form.post("/login")
            .then((data) => {
                const path = { url: LaraPrime.url("/"), remote: true };
                if (data.redirect) {
                    path.url = data.redirect;
                }

                LaraPrime.visit(path);
            })
            .catch((error: AxiosError) => {
                if (error.status! >= 500) {
                    LaraPrime.error(error.message);
                }
            });
    };

    return (
        <>
            <Head title="Log in" />
            <BlockUI blocked={form.processing}>
                <form onSubmit={handleSubmit}>
                    <div className="flex flex-col mb-4">
                        <input
                            type="text"
                            className={classNames(
                                `appearance-none p-3 w-full outline-none border border-transparent text-xl block bg-white/10 text-white/60 rounded-3xl
                        hover:border-white/50 hover:border focus:border-white/90 focus:shadow-[0px_0px_5px] transition duration-250
                        `,
                                {
                                    "border-red-900/80 hover:border-red-900/90 focus:border-red-900 focus:shadow-red-800":
                                        form.hasError("email"),
                                }
                            )}
                            value={form.email}
                            autoComplete="username"
                            placeholder="Email"
                            onInput={(e) =>
                                (form.email = e.currentTarget.value)
                            }
                        />
                        {form.hasError("email") && (
                            <small className="text-red-900 font-bold">
                                {form.getError("email")}
                            </small>
                        )}
                    </div>

                    <div className="flex flex-col mb-4">
                        <input
                            type="password"
                            autoComplete="current-password"
                            className={classNames(
                                `appearance-none p-3 w-full outline-none border border-transparent text-xl block bg-white/10 text-white/60 rounded-3xl
                        hover:border-white/50 hover:border focus:border-white/90 focus:shadow-[0px_0px_5px] transition duration-250
                        `,
                                {
                                    "border-red-900/80 hover:border-red-900/90 focus:border-red-900 focus:shadow-red-800":
                                        form.hasError("password"),
                                }
                            )}
                            placeholder="Password"
                            onInput={(e) =>
                                (form.password = e.currentTarget.value)
                            }
                        />
                        {form.hasError("password") && (
                            <small className="text-red-900 font-bold">
                                {form.getError("password")}
                            </small>
                        )}
                    </div>

                    <div className="flex flex-row justify-between mb-4">
                        <div className="flex items-center">
                            <Checkbox
                                checked={form.remember}
                                onChange={(e) => (form.remember = e.checked)}
                            />
                            <label className="text-white ml-2">
                                Remember me
                            </label>
                        </div>
                        <a className="cursor-pointer font-medium block text-center">
                            Forgot Password?
                        </a>
                    </div>

                    <button
                        type={form.processing ? "button" : "submit"}
                        disabled={form.processing}
                        className={classNames(
                            `appearance-none border-none p-3 w-full outline-none text-xl mb-4 font-medium bg-white/30
                        hover:bg-white/40 active:bg-white/20 text-white/80 cursor-pointer
                        transition duration-250 rounded-3xl hover:shadow-lg`,
                            {
                                "text-white/50 cursor-not-allowe active:bg-white/30 hover:bg-white/30":
                                    form.processing,
                            }
                        )}
                    >
                        {!form.processing ? (
                            "Log In"
                        ) : (
                            <div className="flex flex-row items-center justify-center gap-2">
                                <ProgressSpinner
                                    fill="var(--surface-ground)"
                                    animationDuration=".5s"
                                    strokeWidth="5"
                                    style={{
                                        margin: "0",
                                        width: "1.5rem",
                                        height: "1.5rem",
                                    }}
                                />{" "}
                                <span>Logging in...</span>
                            </div>
                        )}
                    </button>
                </form>
            </BlockUI>
        </>
    );
};

Login.layout = (page) => <AuthLayout children={page} />;

export default Login;
