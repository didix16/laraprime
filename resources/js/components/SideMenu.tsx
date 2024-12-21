import { router } from "@inertiajs/react";
import SideMenuItem from "./SideMenuItem";
export default () => {
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

    const menuItems: SideMenuItem[] = [
        {
            label: "Dashboard",
            icon: "pi pi-home",
            children: [
                {
                    label: "Dashboard 1",
                    icon: "pi pi-home",
                    to: "/dashboard",
                },
                {
                    label: "Dashboard 2",
                    icon: "pi pi-home",
                    to: "/dashboard2",
                },
            ],
        },
        {
            asSeparator: true,
        },
        {
            title: "Admin",
            label: "Users",
            icon: "pi pi-users",
            to: "/users",
        },
        {
            label: "Roles",
            icon: "pi pi-lock",
            to: "/roles",
        },
    ];

    return (
        <nav className="px-4 flex flex-col">
            <ul>
                {menuItems.map((item, idx) => (
                    <SideMenuItem key={`_menu${idx}`} menuItem={item} root />
                ))}
            </ul>
            <ul>
                <li>
                    <button onClick={handleLogout}>
                        <span className="pi pi-power-off"></span>
                        <span>Logout</span>
                    </button>
                </li>
            </ul>
        </nav>
    );
};
