export default class Emitter {
    private events: Record<string, ((...args: any[]) => void)[]> = {};

    $on(event: string, callback: (...args: any[]) => void) {
        if (!this.events[event]) {
            this.events[event] = [];
        }
        this.events[event].push(callback);

        return this;
    }

    $off(event: string, callback: (...args: any[]) => void) {
        const listeners = this.events[event];
        if (listeners) {
            this.events[event] = listeners.filter(
                (listener) => listener !== callback
            );
            listeners.length === 0 && delete this.events[event];
        }

        return this;
    }

    $once(event: string, callback: (...args: any[]) => void) {
        const onceCallback = (...args: any[]) => {
            this.$off(event, onceCallback);
            callback.apply(this, args);
        };

        return this.$on(event, onceCallback);
    }

    $emit(event: string, ...data: any[]) {
        const listeners = this.events[event];
        listeners &&
            listeners.forEach((callback) => {
                callback.apply(this, data);
            });
    }
}
