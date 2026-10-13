(function () {
  'use strict';

  var config = window.buyoraSafepay || null;
  if (!config || !config.startUrl) {
    return;
  }

  var form = document.getElementById('goOrder');
  var paymentType = document.getElementById('buyora-payment-type');
  var cardSection = document.getElementById('buyora-card-pay');
  var bankSubmit = document.getElementById('buyora-bank-submit');
  var payButton = document.getElementById('buyora-pay-securely');
  var hostedFallback = document.getElementById('buyora-hosted-fallback');
  var statusEl = document.getElementById('buyora-card-status');
  var cardAtom = document.getElementById('buyora-safepay-card');
  var payerAuthAtom = document.getElementById('buyora-safepay-auth');
  var modal = document.getElementById('buyora-threeds-modal');
  var closeModalBtn = document.getElementById('buyora-threeds-close');
  var payLabel = payButton ? payButton.querySelector('.buyora-pay-label') : null;

  var busy = false;
  var session = null;
  var cardReady = false;
  var tokenRefreshAttempts = 0;
  var refreshBtn = document.getElementById('buyora-card-refresh');

  function setStatus(message, kind) {
    if (!statusEl) {
      return;
    }
    if (!message) {
      statusEl.hidden = true;
      statusEl.textContent = '';
      statusEl.className = 'buyora-card-status';
      return;
    }
    statusEl.hidden = false;
    statusEl.textContent = message;
    statusEl.className = 'buyora-card-status' + (kind ? ' is-' + kind : '');
  }

  function defaultPayLabel() {
    if (session && session.amount_label) {
      return 'Pay Securely · ' + session.amount_label;
    }
    return 'Pay Securely';
  }

  function setBusy(nextBusy, label) {
    busy = !!nextBusy;
    if (payButton) {
      payButton.disabled = busy || !cardReady;
      payButton.classList.toggle('is-loading', busy);
    }
    if (hostedFallback) {
      hostedFallback.disabled = busy;
      hostedFallback.setAttribute('aria-disabled', busy ? 'true' : 'false');
    }
    if (payLabel) {
      payLabel.textContent = label || defaultPayLabel();
    }
  }

  function syncPaymentMode() {
    var isSafepay = !paymentType || paymentType.value === 'Safepay';
    if (cardSection) {
      cardSection.hidden = !isSafepay;
    }
    if (bankSubmit) {
      bankSubmit.hidden = isSafepay;
    }
  }

  function openModal() {
    if (modal) {
      modal.hidden = false;
      document.body.classList.add('buyora-threeds-open');
    }
  }

  function closeModal() {
    if (modal) {
      modal.hidden = true;
      document.body.classList.remove('buyora-threeds-open');
    }
  }

  function finishSuccess() {
    closeModal();
    setStatus('Payment authorized. Confirming with Buyora…', 'ok');
    if (session && session.return_url) {
      window.location.href = session.return_url;
      return;
    }
    setBusy(false);
    setStatus('Payment finished, but the return link was missing. Contact support with your order details.', 'error');
  }

  function finishFailure(message) {
    closeModal();
    setBusy(false);
    setStatus(message || 'Payment failed. Check your card details and try again.', 'error');
  }

  function remountCardAtom() {
    var frame = document.querySelector('.buyora-card-frame');
    if (!frame) {
      return null;
    }
    var next = document.createElement('safepay-card-atom');
    next.id = 'buyora-safepay-card';
    frame.innerHTML = '';
    frame.appendChild(next);
    cardAtom = next;
    cardReady = false;
    return cardAtom;
  }

  function isTokenAuthError(message) {
    return /jwt|strategies\/|webhook secret|malformed|token expired|tbt/i.test(message || '');
  }

  function configureAtoms(payload) {
    if (!payerAuthAtom) {
      throw new Error('Safepay card components failed to load. Refresh the page or use hosted checkout.');
    }

    remountCardAtom();
    if (!cardAtom) {
      throw new Error('Safepay card components failed to load. Refresh the page or use hosted checkout.');
    }

    Object.assign(payerAuthAtom, {
      environment: payload.environment || config.environment || 'sandbox',
      tracker: payload.tracker,
      authToken: payload.auth_token,
      billing: payload.billing || payerAuthAtom.billing || {},
      authorizationOptions: {
        do_capture: true,
        do_card_on_file: false
      },
      onPayerAuthenticationFrictionless: finishSuccess,
      onPayerAuthenticationSuccess: finishSuccess,
      onPayerAuthenticationFailure: function () {
        finishFailure('Bank authentication failed. You can try another card.');
      },
      onPayerAuthenticationUnavailable: function () {
        finishFailure('Bank authentication is unavailable right now. Try again or use hosted checkout.');
      },
      onSafepayError: function () {
        finishFailure('Safepay reported an error during authentication.');
      }
    });

    Object.assign(cardAtom, {
      environment: payload.environment || config.environment || 'sandbox',
      tracker: payload.tracker,
      authToken: payload.auth_token,
      validationEvent: 'submit',
      inputStyle: {
        fontFamily: 'Inter, system-ui, sans-serif',
        fontSize: '16px',
        color: '#111827'
      },
      onReady: function () {
        cardReady = true;
        tokenRefreshAttempts = 0;
        if (!busy) {
          setBusy(false);
          setStatus('', '');
        }
      },
      onError: function (error) {
        var raw = typeof error === 'string' ? error : (error && error.message ? error.message : '');
        if (isTokenAuthError(raw) && tokenRefreshAttempts < 2 && !busy) {
          tokenRefreshAttempts += 1;
          setStatus('Refreshing secure card fields…', '');
          prepareCardFields();
          return;
        }
        finishFailure(
          isTokenAuthError(raw)
            ? 'Secure card fields expired. Tap Cancel to reload them, or use hosted checkout.'
            : raw || 'Card validation failed.'
        );
      },
      onValidated: function () {
        setStatus('Card details look valid. Authenticating…', 'ok');
      },
      onDiscountApplied: function (data) {
        if (data && data.discountBody) {
          payerAuthAtom.discountBody = data.discountBody;
        }
      },
      onProceedToAuthentication: function (data) {
        payerAuthAtom.deviceDataCollectionJWT = data.accessToken;
        payerAuthAtom.deviceDataCollectionURL = data.deviceDataCollectionURL;
        openModal();
      }
    });
  }

  function formBody(extra) {
    var data = new FormData(form);
    data.set('payment_type', 'Safepay');
    if (config.csrfName && config.csrfHash) {
      data.set(config.csrfName, config.csrfHash);
    }
    Object.keys(extra || {}).forEach(function (key) {
      data.set(key, extra[key]);
    });
    return data;
  }

  function postJson(url, body) {
    return fetch(url, {
      method: 'POST',
      body: body,
      credentials: 'same-origin',
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    }).then(function (response) {
      return response.text().then(function (text) {
        var json = null;
        try {
          json = text ? JSON.parse(text) : null;
        } catch (parseError) {
          throw new Error('Checkout could not start. Refresh and try again, or use hosted checkout.');
        }
        return { httpOk: response.ok, body: json };
      });
    });
  }

  function prepareCardFields() {
    if (!config.prepareUrl || !form) {
      return;
    }
    setStatus('Loading secure card fields…', '');
    postJson(config.prepareUrl, formBody({}))
      .then(function (result) {
        if (!result.body || !result.body.ok) {
          throw new Error((result.body && result.body.error) || 'Secure card fields could not load.');
        }
        session = result.body;
        configureAtoms(session);
        setStatus('Enter your card details below. Your card number never touches Buyora’s servers.', '');
        setBusy(false);
      })
      .catch(function (error) {
        cardReady = false;
        setBusy(false);
        setStatus(
          (error && error.message ? error.message : 'Secure card fields failed to load.') +
            ' You can still use Safepay hosted checkout.',
          'error'
        );
        if (payButton) {
          payButton.disabled = true;
        }
      });
  }

  function startPayment(mode) {
    if (busy) {
      return;
    }
    if (!form.reportValidity()) {
      setStatus('Complete the required delivery fields first.', 'error');
      return;
    }
    if (mode !== 'hosted' && !cardReady) {
      setStatus('Secure card fields are still loading. Wait a moment or use hosted checkout.', 'error');
      return;
    }

    setBusy(true, mode === 'hosted' ? 'Opening Safepay…' : 'Creating order…');
    setStatus(mode === 'hosted' ? 'Creating a secure Safepay session…' : 'Saving your order, then charging the card…', '');

    postJson(config.startUrl, formBody({ safepay_mode: mode || 'embedded' }))
      .then(function (result) {
        if (!result.body || !result.body.ok) {
          throw new Error((result.body && result.body.error) || 'Could not start Safepay.');
        }
        session = result.body;
        if (session.mode === 'hosted') {
          window.location.href = session.checkout_url;
          return null;
        }
        configureAtoms(session);
        setBusy(true, 'Processing…');
        setStatus('Validating card details…', '');
        return waitForCardReady(12000).then(function () {
          setStatus('Validating card details…', '');
          // fetchValidity can hang on empty iframe fields; race it so the shopper gets a clear error.
          var validity = Promise.resolve(cardAtom.fetchValidity()).catch(function () {
            return false;
          });
          var quickCheck = Promise.race([
            validity,
            new Promise(function (resolve) {
              window.setTimeout(function () {
                resolve(false);
              }, 3500);
            })
          ]);
          return quickCheck.then(function (isValid) {
            if (!isValid) {
              throw new Error('Enter a valid card number, expiry date, and security code.');
            }
            setStatus('Charging your card…', '');
            cardAtom.submit();
            window.setTimeout(function () {
              if (busy && (!modal || modal.hidden)) {
                finishFailure('Payment timed out. Check the card fields or use hosted checkout.');
              }
            }, 20000);
          });
        });
      })
      .catch(function (error) {
        finishFailure(error && error.message ? error.message : 'Payment could not be started.');
      });
  }

  function waitForCardReady(timeoutMs) {
    return new Promise(function (resolve, reject) {
      if (cardReady) {
        resolve();
        return;
      }
      var started = Date.now();
      var timer = window.setInterval(function () {
        if (cardReady) {
          window.clearInterval(timer);
          resolve();
          return;
        }
        if (Date.now() - started > (timeoutMs || 12000)) {
          window.clearInterval(timer);
          reject(new Error('Secure card fields are still loading. Wait a moment or use hosted checkout.'));
        }
      }, 100);
    });
  }

  if (paymentType) {
    paymentType.addEventListener('change', syncPaymentMode);
  }
  syncPaymentMode();

  if (payButton) {
    payButton.addEventListener('click', function () {
      startPayment('embedded');
    });
  }

  if (hostedFallback) {
    hostedFallback.addEventListener('click', function (event) {
      event.preventDefault();
      if (!busy) {
        startPayment('hosted');
      }
    });
  }

  if (closeModalBtn) {
    closeModalBtn.addEventListener('click', function () {
      finishFailure('Authentication cancelled. You can try again.');
    });
  }

  if (refreshBtn) {
    refreshBtn.addEventListener('click', function () {
      if (!busy) {
        tokenRefreshAttempts = 0;
        prepareCardFields();
      }
    });
  }

  if (form) {
    form.addEventListener('submit', function (event) {
      if (paymentType && paymentType.value === 'Safepay') {
        event.preventDefault();
        startPayment('embedded');
      }
    });
  }

  setBusy(true, 'Loading…');
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', prepareCardFields);
  } else {
    prepareCardFields();
  }
})();
