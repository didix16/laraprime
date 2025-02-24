import React from "react";

export type DynamicComponentProps = {
    component: string;
    props?: Record<string, any>;
};

const DynamicComponent = ({ component, props }: DynamicComponentProps) => {
    const Component = LaraPrime.component(component) || component;

    if (typeof Component === "string") {
        return React.createElement(Component, props);
    }

    if (!Component) {
        console.error(`Component ${component} not found`);
        return null;
    }

    return <Component {...props} />;
};

export default DynamicComponent;
