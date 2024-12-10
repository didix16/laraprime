import { LayoutProps } from "@/types";
import { ReactNode } from "react";

export default ({ children }: LayoutProps) => {
    return <div id="laraprime">{children as ReactNode}</div>;
};
