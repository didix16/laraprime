import AppLogo from "@/components/AppLogo";
import Gravatar from "@/components/Gravatar";
import { router } from "@inertiajs/react";
import { SplitButton } from "primereact/splitbutton";

export default () => {
    const user = LaraPrime.currentUser();

    const handleLogout = async () => {
        if (
            await LaraPrime.confirm({
                message: "Are you sure you want to logout?",
            })
        ) {
            LaraPrime.logout()
                .then((redirect) => {
                    if (redirect) {
                        window.location.href = redirect;
                        return;
                    }
                    LaraPrime.redirectToLogin();
                })
                .catch(() => {
                    router.reload();
                });
        }
    };

    const startContent = <AppLogo className="w-12 h-12 animate-shine" />;

    const centerContent = (
        <div className="flex flex-wrap align-items-center gap-3">
            <button className="p-link inline-flex justify-content-center align-items-center text-white h-3rem w-3rem border-circle hover:bg-white-alpha-10 transition-all transition-duration-200">
                <i className="pi pi-home text-2xl"></i>
            </button>
            <button className="p-link inline-flex justify-content-center align-items-center text-white h-3rem w-3rem border-circle hover:bg-white-alpha-10 transition-all transition-duration-200">
                <i className="pi pi-user text-2xl"></i>
            </button>
            <button className="p-link inline-flex justify-content-center align-items-center text-white h-3rem w-3rem border-circle hover:bg-white-alpha-10 transition-all transition-duration-200">
                <i className="pi pi-search text-2xl"></i>
            </button>
        </div>
    );

    const endContent = (
        <SplitButton
            pt={{
                icon: {
                    className: "text-white",
                },
                button: {
                    root: {
                        className:
                            "hover:border-white hover:shadow-[0px_0px_5px] hover:bg-white/15 hover:[text-shadow:_0px_0px_10px_var(--lp-shadow-color)]",
                    },
                    label: { className: "text-slate-300 " },
                },
                menuButton: {
                    root: {
                        className:
                            "hover:border-white hover:shadow-[0px_0px_5px] hover:bg-white/15 hover:[text-shadow:_0px_0px_10px_var(--lp-shadow-color)]",
                    },
                },
            }}
            text
            label={user?.name}
            icon={
                <Gravatar className="mr-2" email={user!.email} shape="circle" />
            }
            model={[
                {
                    label: "Logout",
                    icon: "pi pi-sign-out",
                    command: handleLogout,
                },
            ]}
        ></SplitButton>
    );
    /* <Toolbar
            start={startContent}
            center={centerContent}
            end={endContent}
            className="w-full bg-gray-900 shadow-2 bg-linear-225 from-slate-500 to-slate-800"
        /> */
    /**
     * -bg-linear-225 from-slate-500 from-[224px] to-slate-800
     */
    return (
        <header className="w-full shadow-[0px_0px_5px] [box-shadow:_0px_0px_10px_var(--lp-shadow-color)]">
            <nav className="flex justify-between items-center bg-slate-800  px-4 py-2">
                <div className="min-w-52 flex justify-center">
                    {startContent}
                </div>
                {centerContent}
                {endContent}
            </nav>
        </header>
    );
};
