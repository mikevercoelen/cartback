import 'core-js/stable'
import 'regenerator-runtime/runtime'
import 'whatwg-fetch';

(function () {
  const POST_URL = '/wp-json/inject-checkout-scripts/v1/register-email'
  const billingEmailInput = document.getElementById('billing_email')

  function validateEmail (email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    return re.test(String(email).toLowerCase())
  }

  const getEmail = () => {
    return billingEmailInput.value
  }

  const wasAutoFilled = () => new Promise(resolve => {
    setTimeout(() => {
      const email = getEmail()
      const isValid = validateEmail(email)

      if (isValid) {
        return resolve(true)
      }

      resolve(false)
    }, 100)
  })

  const registerEmail = async (email) => {
    try {
      await fetch(POST_URL, {
        method: 'POST',
        body: JSON.stringify({
          email
        })
      })
    } catch {
      // Ignore errors.
    }
  }

  async function handleBlur () {
    const email = billingEmailInput.value
    const isValidEmail = validateEmail(email)

    if (email === '' || !isValidEmail) {
      return null
    }

    await registerEmail(email)
  }

  const checkAutoFilling = async () => {
    const autoFilled = await wasAutoFilled()

    if (!autoFilled) {
      return
    }

    const email = getEmail()
    await registerEmail(email)
  }

  checkAutoFilling()
  billingEmailInput.addEventListener('blur', handleBlur)
})()
