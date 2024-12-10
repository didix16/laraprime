import { LayoutProps } from "@/types";
import { ReactNode } from "react";

export default ({ children }: LayoutProps) => {
    return (
        <div
            className={`px-4 py-8 md:px-6 lg:px-8 flex items-center justify-center -bg-linear-225 from-slate-600 via-slate-400
                via-45% to-slate-800 h-dvh
            `}
            /* style={{
                background:
                    "linear-gradient(-225deg, rgb(172, 50, 228), rgb(121, 24, 242) 48%, rgb(72, 1, 255))",
                backgorund:"linear-gradient(-225deg, rgb(72 72 73), rgb(134 133 135) 48%, rgb(43 42 44))"
            }} */
        >
            <div
                className={`p-6 shadow-2 text-center lg:w-96 animate-fade-down animate-once animate-ease-in-out animate-alternate
                    rounded-xl bg-white/10 text-white/80`}
            >
                <div className="text-4xl font-medium mb-6">Welcome</div>
                {children as ReactNode}
            </div>
        </div>
    );
};
