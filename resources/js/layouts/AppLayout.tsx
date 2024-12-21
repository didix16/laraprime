import Sidebar from "@/layouts/Sidebar";
import { LayoutProps } from "@/types";
import { useMountEffect, useUnmountEffect } from "primereact/hooks";
import { ReactNode } from "react";
import Main from "./Main";

export default ({ children }: LayoutProps) => {
    const handleTokenExpired = () => {
        LaraPrime.error("Your session has expired. Please login again.");
        setTimeout(() => {
            LaraPrime.redirectToLogin();
        }, 4000);
    };

    const handleError = (message: string) => {
        LaraPrime.error(message);
    };

    useMountEffect(() => {
        LaraPrime.$on("error", handleError);
        LaraPrime.$on("token-expired", handleTokenExpired);
    });

    useUnmountEffect(() => {
        LaraPrime.$off("error", handleError);
        LaraPrime.$off("token-expired", handleTokenExpired);
    });
    return (
        <div id="laraprime" className="flex flex-row items-start h-dvh w-full">
            <Sidebar />
            <Main>{children as ReactNode}</Main>
        </div>
    );
};
