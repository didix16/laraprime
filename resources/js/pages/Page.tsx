import { DynamicPageProps } from "@/types";
import { Card } from "primereact/card";

const Page = ({ l }: DynamicPageProps) => {
    console.log("Page::components", l);

    return (
        <Card className="flex-1">
            <h1>This is a Dynamic Page</h1>
        </Card>
    );
};

export default Page;
