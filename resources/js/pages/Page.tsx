import DynamicComponent from "@/components/DynamicComponent";
import { DynamicPageProps } from "@/types";
import { Card } from "primereact/card";

const Page = ({ l }: DynamicPageProps) => {
    console.log("Page::components", l);

    return (
        <Card className="flex-1">
            <h1>This is a Dynamic Page</h1>
            <DynamicComponent component={l.n} props={l.p} children={l.c} />
        </Card>
    );
};

export default Page;
