import React from "react";

interface DynamicComponentProps {
    component: string;
    props: any;
    children?: Array<{ n: string; p: any; c?: any } | string>;
}

const DynamicComponent = ({
    component,
    props,
    children,
}: DynamicComponentProps) => {
    const Component = LaraPrime.component(component) || component;

    console.log("rendering", component, Component, props);
    if (typeof Component === "string" && children) {
        React.createElement(
            Component,
            props,
            children?.map((child, index) => (
                <DynamicComponent
                    key={index}
                    component={child.n || child}
                    props={child.p}
                    children={child.c}
                />
            ))
        );
    } else if (typeof Component === "string") {
        return Component;
    }

    if (!Component) {
        console.error(`Component ${component} not found`);
        return null;
    }

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
};

export default DynamicComponent;
