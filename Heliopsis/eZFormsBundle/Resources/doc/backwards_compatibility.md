# Backwards Compatibility Notes

## 1.0 to 1.1

- Location and content are now injected inside FormHandlers directly from the controller (by way of
  LocationAwareHandlerInterface and ContentAwareHandlerInterface).
  ContentService dependency has therefore been moved from various handlers to the controller.