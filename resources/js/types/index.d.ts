import { router } from "@inertiajs/react";
import React, { ReactNode } from "react";

export type PageComponent = {
    (): React.JSX.Element;
    layout?: (
        page: PageComponent
    ) => ReactNode | React.FC<{ page: PageComponent }> | PageComponent;
};
interface ChildrenWithClassName {
    children?: PageComponent | ReactNode;
    className?: string;
}

export interface LayoutProps extends ChildrenWithClassName {
    theme?: strings;
}

export type VisitCallback = typeof router.visit;
export type InferArrayType<T> = T extends (infer U)[] ? U : never;
export type MergeTypes<T extends unknown[]> = T extends [
    a: infer A,
    ...rest: infer R
]
    ? A & MergeTypes<R>
    : {};
