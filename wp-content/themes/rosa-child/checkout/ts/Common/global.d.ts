declare const MAPBOX_API_ACCESS_TOKEN: string;
declare const OLO_API_BASE_URL: string;
declare const OLO_MAKE_LABEL: string;
declare const OLO_MODEL_LABEL: string;
declare const OLO_COLOR_LABEL: string;
declare const SPREEDLY_ENVIRONMENT_KEY: string;
declare const SENTRY_DSN: string;
declare const SENTRY_ENVIRONMENT: string;
declare const SENTRY_RELEASE: string;

declare module NodeJS {
  interface Global {
    MAPBOX_API_ACCESS_TOKEN: string;

    OLO_API_BASE_URL: string;
    OLO_MAKE_LABEL: string;
	  OLO_MODEL_LABEL: string;
	  OLO_COLOR_LABEL: string;

    SPREEDLY_ENVIRONMENT_KEY: string;
    SENTRY_DSN: string;
    SENTRY_ENVIRONMENT: string;
    SENTRY_RELEASE: string;
  }
}
