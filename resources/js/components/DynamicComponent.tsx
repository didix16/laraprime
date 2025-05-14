import React from "react";

interface DynamicComponentProps {
    component: string;
    props: any;
    children?: Array<{ n: string; p: any; c?: any }>;
}

const DynamicComponent = ({
    component,
    props,
    children,
}: DynamicComponentProps) => {
    const Component = LaraPrime.component(component) || component;

    const isString = typeof Component === "string";
    if (isString && component === "#t") {
        return props["d"] ?? "";
    } else if (Component) {
        return React.createElement(
            Component,
            props,
            children?.map((child, index) => (
                <DynamicComponent
                    key={index}
                    component={child.n}
                    props={child.p}
                    children={child.c}
                />
            ))
        );
    } else {
        console.error(`Component ${component} not found`);
        return null;
    }
};

export default DynamicComponent;
