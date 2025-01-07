import { Gravatar } from "@/types";
import { hash256 } from "@/utils";
import { Avatar } from "primereact/avatar";
import { useEffect, useState } from "react";

export default (props: Gravatar) => {
    const [image, setImage] = useState<string>("");

    useEffect(() => {
        (async () => {
            if (props.email) {
                setImage(
                    `https://www.gravatar.com/avatar/${await hash256(
                        props.email
                    )}?d=mp`
                );
            }
        })();
    }, []);

    return <Avatar {...{ ...props, image }} />;
};
