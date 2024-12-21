import { InferArrayType } from "@/types";
import clsx, { ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";
/**
 * Url helper function to build a url with query parameters
 * @param base
 * @param path
 * @param params
 * @returns the url with query parameters
 */
export const url = (
    base: string,
    path: string,
    params: InferArrayType<ConstructorParameters<typeof URLSearchParams>>
) => {
    const searchParams = new URLSearchParams(params);
    const queryString = searchParams.toString();

    base = base === "/" && path.startsWith("/") ? "" : base;
    return `${base}${path}${queryString.length > 0 ? `?${queryString}` : ""}`;
};

/**
 * Helper function to check if the object is a file, file list or blob
 * @param obj
 * @returns true if the object is a file, file list or blob
 */
export const isFile = (obj: any) => {
    return (
        obj instanceof File || obj instanceof FileList || obj instanceof Blob
    );
};

/**
 * Helper function to merge two objects deeply
 * Target object will be modified in place with the source object keys and values
 * @param target
 * @param source
 */
export const mergeObject = (
    target: Record<string, any>,
    source: Record<string, any>
) => {
    for (const key in source) {
        target[key] = cloneDeep(source[key]);
    }
};

/**
 * Helper function to clone an object deeply
 * @param obj
 * @returns a deep clone of the object passed
 */
export const cloneDeep = (obj: Record<string, any>) => {
    if (obj === null || isFile(obj)) {
        return obj;
    }

    if (Array.isArray(obj)) {
        const clone: Array<Object> = [];

        for (const item in obj) {
            if (obj.hasOwnProperty(item)) {
                clone[item] = cloneDeep(obj[item]);
            }
        }

        return clone;
    }

    if (typeof obj === "object") {
        const clone: Record<string, any> = {};

        for (const item in obj) {
            if (obj.hasOwnProperty(item)) {
                clone[item] = cloneDeep(obj[item]);
            }
        }

        return clone;
    }

    return obj;
};

const appendToFormData = (formData: FormData, key: string, value: any) => {
    if (value instanceof Date) {
        formData.append(key, value.toISOString());
    } else if (value instanceof File) {
        formData.append(key, value, value.name);
    } else if (typeof value === "boolean") {
        formData.append(key, value ? "1" : "0");
    } else if (value === null || typeof value === "undefined") {
        formData.append(key, "");
    } else if (typeof value !== "object") {
        formData.append(key, value);
    } else {
        objectToFormData(value, formData, key);
    }
};

export const objectToFormData = (
    obj: Record<string, any> | string | null,
    formData = new FormData(),
    parent: string = ""
) => {
    if (
        obj === null ||
        obj === "undefined" ||
        (obj.hasOwnProperty("length") &&
            (obj as { length: number }).length === 0)
    ) {
        formData.append(parent, obj as string);
        return formData;
    }

    for (const property in obj as Record<string, any>) {
        if (obj.hasOwnProperty(property)) {
            appendToFormData(
                formData,
                parent ? `${parent}[${property}]` : property,
                (obj as Record<string, any>)[property]
            );
        }
    }

    return formData;
};

/**
 * Helper function that combines tailwindcss and clsx to merge classes
 * @param classes
 * @returns
 */
export const classNames = (...classes: ClassValue[]) => {
    return twMerge(clsx(classes));
};
