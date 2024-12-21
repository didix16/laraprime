import { ChildrenWithClassName } from "@/types";
import { ReactNode } from "react";

export default ({ children }: ChildrenWithClassName) => {
    return <main className="px-4">{children as ReactNode}</main>;
};
