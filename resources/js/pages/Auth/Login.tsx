//import { PrimeReactContext } from "primereact/api";
//import { useContext } from "react";

import useLaraForm from "@/hooks/useLaraForm";
import AuthLayout from "@/layouts/AuthLayout";
import { PageComponent } from "@/types";
import { Head } from "@inertiajs/react";
import { FormEvent } from "react";

const Login: PageComponent = () => {
    //const { changeTheme } = useContext(PrimeReactContext);

    // if(changeTheme){
    //     changeTheme(window.LaraPrime.config('theme'), 'lara-dark-cyan', 'theme-link');
    // }

    const { form } = useLaraForm({
        email: "",
        password: "",
        rememberMe: false,
    });

    const handleSubmit = (e: FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        form.post("/login")
            .then((data) => {
                console.log(data);
            })
            .catch((error) => {
                console.log(error);
                console.log(form);
                LaraPrime.error(error.message);
            });
    };

    return (
        <>
            <Head title="Log in" />
            <form onSubmit={handleSubmit}>
                <input
                    type="text"
                    className={`appearance-none p-3 w-full outline-none border border-transparent text-xl block mb-4 bg-white/10 text-white/60 rounded-3xl
                        hover:border-white/50 hover:border focus:border-white/90 focus:shadow-[0px_0px_5px] transition duration-250
                        `}
                    value={form.email}
                    autoComplete="username"
                    placeholder="Email"
                    onInput={(e) => (form.email = e.currentTarget.value)}
                />
                <input
                    type="password"
                    autoComplete="current-password"
                    className={`appearance-none p-3 w-full outline-none border border-transparent text-xl block mb-4 bg-white/10 text-white/60 rounded-3xl
                        hover:border-white/50 hover:border focus:border-white/90 focus:shadow-[0px_0px_5px] transition duration-250
                        `}
                    placeholder="Password"
                    onInput={(e) => (form.password = e.currentTarget.value)}
                />
                <button
                    type="submit"
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
