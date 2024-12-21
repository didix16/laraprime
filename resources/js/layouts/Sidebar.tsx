import AppLogo from "@/components/AppLogo";
import SideMenu from "@/components/SideMenu";
export default () => {
    return (
        <aside className="bg-linear-180 from-slate-500 to-slate-800 shadow flex flex-col min-h-screen min-w-56">
            <div className="flex w-full justify-center my-4">
                <AppLogo className="w-16 h-16 animate-shine" />
            </div>
            <SideMenu />
        </aside>
    );
};
