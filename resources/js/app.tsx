import "@/../sass/app.scss";
import LaraPrime, { LaraPrimeConfig } from "@/LaraPrime";

window.createLaraPrimeApp = (config: LaraPrimeConfig) => new LaraPrime(config);
