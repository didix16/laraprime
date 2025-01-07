import { ChildrenWithClassName } from "@/types";
import { BreadCrumb } from "primereact/breadcrumb";
import { ReactNode } from "react";
import { HiHome } from "react-icons/hi2";

export default ({ children }: ChildrenWithClassName) => {
    return (
        <main className="px-4 w-full bg-linear-180 from-slate-500 to-slate-800">
            <BreadCrumb
                className="mb-4"
                home={{
                    icon: <HiHome />,
                    url: "/",
                }}
                model={[
                    {
                        label: "Dashboard",
                        icon: "pi pi-home",
                        url: "/main",
                    },
                ]}
            />
            {children as ReactNode}
        </main>
    );
};
