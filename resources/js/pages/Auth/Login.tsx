//import { PrimeReactContext } from "primereact/api";
//import { useContext } from "react";

import AuthLayout from "@/layouts/AuthLayout";
import { PageComponent } from "@/types";
import { Head } from "@inertiajs/react";
import { useState } from "react";

const Login: PageComponent = () => {
    //const { changeTheme } = useContext(PrimeReactContext);

    // if(changeTheme){
    //     changeTheme(window.LaraPrime.config('theme'), 'lara-dark-cyan', 'theme-link');
    // }

    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");

    return (
        <>
            <Head title="Log in" />
            <form>
                <input
                    type="text"
                    className={`appearance-none p-3 w-full outline-none border border-transparent text-xl block mb-4 bg-white/10 text-white/60 rounded-3xl
                        hover:border-white/50 hover:border focus:border-white/90 transition duration-250
                        `}
                    value={email}
                    autoComplete="username"
                    placeholder="Email"
                    onInput={(e) => setEmail(e.currentTarget.value)}
                />
                <input
                    type="password"
                    autoComplete="current-password"
                    className={`appearance-none p-3 w-full outline-none border border-transparent text-xl block mb-4 bg-white/10 text-white/60 rounded-3xl
                        hover:border-white/50 hover:border focus:border-white/90 transition duration-250
                        `}
                    placeholder="Password"
                    onInput={(e) => setPassword(e.currentTarget.value)}
                />
                <button
                    type="button"
                    className={`appearance-none border-none p-3 w-full outline-none text-xl mb-4 font-medium bg-white/30
                        hover:bg-white/40 active:bg-white/20 text-white/80 cursor-pointer
                        transition duration-250 rounded-3xl hover:shadow-lg`}
                >
                    Log In
                </button>
                <a className="cursor-pointer font-medium block text-center">
                    Forgot Password?
                </a>
            </form>
        </>
    );
};

Login.layout = (page) => <AuthLayout children={page} />;

export default Login;
