import { useEffect, useRef, useState } from "react";

export default (initialData: Record<string, any>) => {
    const form = useRef(LaraPrime.form(initialData));

    const [formState, setFormState] = useState({
        ...form.current.data(),
    });

    useEffect(() => {
        form.current = new Proxy(form.current, {
            set(target, key, value) {
                target[key] = value;
                setFormState({
                    ...form.current.data(),
                });
                return true;
            },
        });

        return () => {
            form.current;
        };
    }, [form]);

    return {
        form: form.current,
    };
};
