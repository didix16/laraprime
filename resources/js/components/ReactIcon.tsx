import { ReactIconProps } from "@/types";
import { createElement, useEffect, useState } from "react";
import { IconType } from "react-icons";

export default ({ name, size, color, className }: ReactIconProps) => {
    if (!name) return null;

    const [iconComponent, setIconComponent] = useState<IconType | null>(null);

    const libraryMap = {
        hi2: import("react-icons/hi2"),
    } as Record<string, Promise<any>>;

    useEffect(() => {
        // Divde the name into package and icon name
        //const [packageName, iconName] = name.split(/(?=[A-Z])/); // Ejemplo: "FaBeer" -> ["Fa", "Beer"]
        const [packageName, iconName] = name.split(/\//); // Ejemplo: "hi2/HiBeer" -> ["hi2", "HiBeer"]

        // Dynamically import the package
        libraryMap[packageName]
            .then((module) => {
                const Icon: IconType | undefined = module[iconName] as IconType;
                if (Icon) {
                    setIconComponent(() => Icon);
                } else {
                    console.error(
                        `${iconName} Icon not found on ${packageName} package`
                    );
                }
            })
            .catch((err) =>
                console.error(`Error while loading ${iconName} icon:`, err)
            );
    }, [name]);

    if (!iconComponent) return null;

    // Renderizar el componente del icono dinámico
    return createElement(iconComponent, { size, color, className });
};
