import ReactIcon from "@/components/ReactIcon";
import { ChildrenWithClassName, LaraPrimeBreadcrumbItem } from "@/types";
import { Link } from "@inertiajs/react";
import { BreadCrumb } from "primereact/breadcrumb";
import { MenuItem, MenuItemOptions } from "primereact/menuitem";
import { ReactNode } from "react";
import { HiHome } from "react-icons/hi2";

export default ({ children }: ChildrenWithClassName) => {
    const breadcrumbs = (
        LaraPrime.config("breadcrumbs") as LaraPrimeBreadcrumbItem[]
    )?.map((breadcrumb) => {
        breadcrumb.template = (item: MenuItem, options: MenuItemOptions) => {
            return (
                <Link href={item.url as string} className={options.className}>
                    {breadcrumb.icon && (
                        <ReactIcon name={breadcrumb.icon as string} />
                    )}
                    {breadcrumb.showLabel && item.label}
                </Link>
            );
        };
        return breadcrumb;
    });

    return (
        <main className="flex flex-col px-4 w-full bg-linear-180 from-slate-500 to-slate-800">
            {breadcrumbs && (
                <BreadCrumb
                    className="mb-4"
                    home={{
                        template: (
                            item: MenuItem,
                            options: MenuItemOptions
                        ) => {
                            return (
                                <Link
                                    href={LaraPrime.url("/")}
                                    className={options.className}
                                >
                                    <HiHome />
                                </Link>
                            );
                        },
                    }}
                    model={breadcrumbs}
                />
            )}
            {children as ReactNode}
        </main>
    );
};
