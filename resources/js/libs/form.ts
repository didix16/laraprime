import { mergeObject, objectToFormData } from "@/utils";
import type { AxiosError, AxiosInstance } from "axios";

export type FormDataType = Record<string, any> | Array<string>;
export type FormOptions<T = any> = {
    resetOnSuccess?: boolean;
    onSuccess?: (this: LaraForm, data: T) => void;
    onFail?: (this: LaraForm, error: AxiosError<T>) => void;
    httpLib?: AxiosInstance;
};
export type MesageErrorBag = Record<string, Array<string>>;
export type FormRequestMethods =
    | "get"
    | "post"
    | "put"
    | "patch"
    | "delete"
    | "head";

export class FormErrors {
    constructor(protected errors: MesageErrorBag = {}) {}

    /**
     * Determine if any errors exists for the given field or object.
     * @param field
     */
    public has(field: string) {
        const hasError = this.errors.hasOwnProperty(field);

        if (!hasError) {
            const errors = Object.keys(this.errors).filter(
                (key) =>
                    key.startsWith(`${field}.`) || key.startsWith(`${field}[`)
            );
            return errors.length > 0;
        }

        return hasError;
    }

    /**
     * Get all of the errors for the bag.
     * @returns The errors object
     */
    public all() {
        return this.errors;
    }

    /**
     * Return the first error message for the given field.
     * If the field does not have any errors, undefined is returned.
     * @param field The field to get the error message for
     * @returns The first error message for the given field or undefined
     */
    public first(field: string): string | undefined {
        return this.get(field)[0];
    }

    /**
     * Return the error messages for the given field.
     * If the field does not have any errors, an empty array is returned.
     * @param field The field to get the error messages for
     * @returns The error messages for the given field or an empty array
     */
    public get(field: string) {
        return this.errors[field] || [];
    }

    /**
     * If no keys specified, determine if we have any errors.
     * If keys are specified, determine if we have any errors for the given keys
     * and return them.
     * @param keys
     * @returns
     */
    public any(): boolean;
    public any(keys: [string, ...string[]]): MesageErrorBag;
    public any(keys: Array<string> = []): boolean | MesageErrorBag {
        if (keys.length === 0) {
            return Object.keys(this.errors).length > 0;
        }

        return keys.reduce((errors, key) => {
            errors[key] = this.get(key);
            return errors;
        }, {} as MesageErrorBag);
    }

    /**
     * Clear a specific field, object or all errors from the bag.
     * @param field
     * @returns
     */
    public clear(field?: string) {
        if (!field) {
            this.errors = {};
            return;
        }

        Object.keys(this.errors)
            .filter(
                (key) =>
                    key.startsWith(`${field}.`) || key.startsWith(`${field}[`)
            )
            .forEach((key) => {
                delete this.errors[key];
            });
    }

    /**
     * Set the new errors for the bag.
     * @param errors
     * @returns self class for chaining
     */
    public setErrors(errors: MesageErrorBag) {
        this.errors = errors;
        return this;
    }
}

export default class LaraForm {
    [name: string | symbol]: any;
    protected static VALID_METHODS: FormRequestMethods[] = [
        //"get",
        "post",
        "put",
        "patch",
        "delete",
        "head",
    ];
    protected processing: boolean = false;
    protected successful: boolean = false;
    protected initalValues: FormDataType = {};
    protected errors: FormErrors = new FormErrors();
    // internal data storage
    //protected $data: Map<string, any> = new Map<string, any>();
    protected $data: Record<string, any> = {};
    protected options: FormOptions = { resetOnSuccess: true };
    protected $httpLib: AxiosInstance | null = null;

    constructor(data: FormDataType = {}, options: FormOptions = {}) {
        this.withData(data).withOptions(options);
        return new Proxy(this, {
            get(target, prop, receiver) {
                if (prop in target) {
                    return Reflect.get(target, prop, receiver);
                }

                return target.$data[prop as string];
            },
            set(target, prop, value, receiver) {
                if (prop in target) {
                    return Reflect.set(target, prop, value, receiver);
                }

                target.$data[prop as string] = value;
                return true;
            },
        });
    }

    // Make the form data iterable
    [Symbol.iterator]() {
        return Object.entries(this.$data)[Symbol.iterator]();
    }

    /**
     * Initialize the form data with the given data.
     * You can pass an array of field names (values will be initialized as empty strings)
     * or an object with field names as keys.
     * @param data The form data
     * @returns self class for chaining
     */
    public withData(data: string[]): LaraForm;
    public withData(data: Record<string, any>): LaraForm;
    public withData(data: FormDataType) {
        // If data is an array then it should be a field name array, so convert it to an object with empty values
        if (Array.isArray(data)) {
            data = data.reduce((obj, field) => {
                obj[field] = "";
                return obj;
            }, {});
        }

        this.setInitialValues(data);

        for (const field in data) {
            this.$data[field] = (data as Record<string, any>)[field];
        }

        return this;
    }

    public async withOptions(options: FormOptions) {
        options.hasOwnProperty("resetOnSuccess") &&
            (this.options.resetOnSuccess = options.resetOnSuccess);

        options.hasOwnProperty("onSuccess") &&
            (this.options.onSuccess = options.onSuccess);

        options.hasOwnProperty("onFail") &&
            (this.options.onFail = options.onFail);

        // check for axios instance on window
        const globalAxios =
            typeof window === "undefined" ? false : window.axios;

        this.$httpLib =
            options.httpLib || globalAxios || (await import("axios")).default;

        if (!this.$httpLib) {
            throw new Error(
                "No HTTP library provided. Either pass an httpLib option, or install axios."
            );
        }

        return this;
    }

    /**
     * Retrieve the form data.
     * @returns The form data
     */
    public data() {
        return this.$data;
    }

    /**
     * Given a list of form fields, get the subset of the form data
     * that corresponds to the given fields.
     * @param fields
     * @returns The subset of the form data that corresponds to the given fields
     */
    public only(fields: string[]) {
        return fields.reduce((obj, field) => {
            obj[field] = this.$data[field];
            return obj;
        }, {} as Record<string, any>);
    }

    /**
     * Reset the form fields to its initial values. Also clears any errors.
     * @returns self class for chaining
     * @returns
     */
    public reset() {
        mergeObject(this.$data, this.initalValues);
        this.errors.clear();
        return this;
    }

    /**
     * Hydrate the form data with the given data.
     * Only the fields that are present in the form data will be hydrated.
     * @param data
     * @returns
     */
    public populate(data: Record<string, any>) {
        for (const field in data) {
            this.$data.hasOwnProperty(field) &&
                mergeObject(this.$data, { [field]: data[field] });
        }

        return this;
    }

    /**
     * Clear the form fields and errors.
     * @returns self class for chaining
     */
    public clear() {
        // set all entreis values to empty strings
        for (const field in this.$data) {
            this.$data[field] = "";
        }

        this.errors.clear();
        return this;
    }

    /**
     * Determine if any errors exists for the given field or object.
     * @param field
     * @returns true if the field has errors or false otherwise
     */
    public hasError(field: string) {
        return this.errors.has(field);
    }

    /**
     * Return the first error message for the given field.
     * If the field does not have any errors, undefined is returned.
     * @param field The field to get the error message for
     * @returns The first error message for the given field or undefined
     */
    public getError(field: string) {
        return this.errors.first(field);
    }

    /**
     * Return the error messages for the given field.
     * If the field does not have any errors, an empty array is returned.
     * @param field The field to get the error messages for
     * @returns The error messages for the given field or an empty array
     */
    public getErrors(field: string) {
        return this.errors.get(field);
    }

    /**
     * Send a POST request to the given URL.
     * @param url The URL to submit the form data to
     * @returns A promise that resolves with the response data
     */
    public post(url: string) {
        return this.submit("post", url);
    }

    /**
     * Send a PUT request to the given URL.
     * @param url The URL to submit the form data to
     * @returns A promise that resolves with the response data
     */
    public put(url: string) {
        return this.submit("put", url);
    }

    /**
     * Send a PATCH request to the given URL.
     * @param url The URL to submit the form data to
     * @returns A promise that resolves with the response data
     */
    public patch(url: string) {
        return this.submit("patch", url);
    }

    /**
     * Send a DELETE request to the given URL.
     * @param url The URL to submit the form data to
     * @returns A promise that resolves with the response data
     */
    public delete(url: string) {
        return this.submit("delete", url);
    }

    /**
     * Submit the form data to the given URL using the given method.
     * @param method The request method to use
     * @param url The URL to submit the form data to
     * @returns A promise that resolves with the response data
     */
    protected submit(method: FormRequestMethods, url: string) {
        if (LaraForm.VALID_METHODS.indexOf(method) === -1) {
            throw new Error(
                `Method ${method} is not a valid request type. Must be one of ${LaraForm.VALID_METHODS.join(
                    ", "
                )}`
            );
        }

        this.errors.clear();
        this.processing = true;
        this.successful = false;

        return new Promise<any>((resolve, reject) => {
            this.$httpLib![method](
                url,
                this.hasFiles() ? objectToFormData(this.data()) : this.data()
            )
                .then((response) => {
                    this.processing = false;
                    this.successful = true;

                    if (this.options.resetOnSuccess) {
                        this.reset();
                    }

                    this.options.onSuccess &&
                        this.options.onSuccess.apply(this, response.data);

                    resolve(response.data);
                })
                .catch((error) => {
                    this.processing = false;
                    this.successful = false;

                    if (error.response && error.response.data.errors) {
                        this.errors.setErrors(error.response.data.errors);
                    }

                    this.options.onFail &&
                        this.options.onFail.apply(this, error);

                    reject(error);
                });
        });
    }

    public setInitialValues(data: FormDataType) {
        // Merge the data with the initial values
        mergeObject(this.initalValues, data);

        return this;
    }

    /**
     * Check if the form has some field that is a file or a file list.
     * @returns true if the form has some field that is a file or a file list or false otherwise
     */
    protected hasFiles(): boolean {
        for (const key in this.$data) {
            if (this.hasFilesRecursive(this.$data[key])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Recursive function to check if the passed data has files.
     * @param data
     * @returns true if the form data has files or false otherwise
     */
    protected hasFilesRecursive(data: Record<string, any>): boolean {
        if (data === null) return false;
        if (typeof data === "object") {
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    if (this.hasFilesRecursive(data[key])) {
                        return true;
                    }
                }
            }
        }

        if (Array.isArray(data)) {
            for (const item in data) {
                if (data.hasOwnProperty(item)) {
                    return this.hasFilesRecursive(data[item]);
                }
            }
        }

        return data instanceof File || data instanceof FileList;
    }

    /**
     * Factory method to create a new LaraForm instance.
     * @param data The form data
     * @param options The form options
     * @returns
     */
    static create(data: FormDataType = {}, options: FormOptions = {}) {
        return new LaraForm(data, options);
    }
}
