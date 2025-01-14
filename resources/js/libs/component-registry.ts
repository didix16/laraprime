export type ComponentMap = Record<string, React.ComponentType<any>>;

/**
 * A registry for react components
 */
export default class ComponentRegistry {
    protected components: ComponentMap = {};

    /**
     * Register a component with a name to be used later on the application
     * @param name The name of the component
     * @param component The react component to register
     * @returns The ComponentRegistry instance
     */
    public register(name: string, component: React.ComponentType<any>) {
        this.components[name] = component;
        return this;
    }

    /**
     * Get a component registered with the given name
     * @param name The name of the component to get
     * @returns The component registered with the given name or null if not found
     */
    public get(name: string): React.ComponentType<any> | null {
        return this.components[name] || null;
    }
}
