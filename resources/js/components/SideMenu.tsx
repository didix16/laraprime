import ReactIcon from "./ReactIcon";
import SideMenuItem from "./SideMenuItem";
export default () => {
    const menuItems: SideMenuItem[] = [
        {
            label: "Dashboard",
            icon: (
                <ReactIcon
                    className="inline-block mr-2"
                    name="hi2/HiHome"
                    size={20}
                />
            ),
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
        <nav className="px-4 flex flex-col flex-1 h-full justify-between">
            <ul>
                {menuItems.map((item, idx) => (
                    <SideMenuItem key={`_menu${idx}`} menuItem={item} root />
                ))}
            </ul>
        </nav>
    );
};
