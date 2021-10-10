export class OloError extends Error {
  constructor(message: string) {
    super(message);

    // Maintains proper stack trace for where our error was thrown (only available on V8)
    if (Error.captureStackTrace) {
      Error.captureStackTrace(this, OloError)
    }

    this.name = 'OloError';
  }
}

export class SpreedlyError extends Error {
  constructor(message: string) {
    super(message);

    // Maintains proper stack trace for where our error was thrown (only available on V8)
    if (Error.captureStackTrace) {
      Error.captureStackTrace(this, OloError)
    }

    this.name = 'SpreedlyError';
  }
}

export class NetworkError extends Error {
  constructor(message: string) {
    super(message);

    // Maintains proper stack trace for where our error was thrown (only available on V8)
    if (Error.captureStackTrace) {
      Error.captureStackTrace(this, OloError)
    }

    this.name = 'NetworkError';
  }
}