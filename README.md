# SIN-project-2025

Browser-based Speech-in-Noise (SIN) experiment built with jsPsych.

The main task plays a spoken sentence mixed with multitalker babble at an adaptive signal-to-noise ratio (SNR). Participants type what they heard; responses are scored word-by-word (including simple alternative forms like `(a/the)`), and SNR is adjusted trial-to-trial.

The experiment supports two conditions via a URL parameter:

- **treatment**: practice → pre-test → 20-minute training (with feedback + optional replay) → post-test
- **control**: practice → pre-test → 20-minute break → post-test

## Run Locally

From the project folder (the one containing `sin_test.html`), start a local PHP server:

```bash
php -S localhost:8000
```

Then open one of the following URLs:

- Treatment: `http://localhost:8000/sin_test.html?condition=treatment`
- Control: `http://localhost:8000/sin_test.html?condition=control`

If `condition` is omitted, the experiment defaults to `treatment`.
