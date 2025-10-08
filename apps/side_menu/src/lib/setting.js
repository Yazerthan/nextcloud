const waitPasswordConfirmation = async () => {
  let tries = 0

  return new Promise((resolve, reject) => {
    const execute = () => {
      if (!OC.PasswordConfirmation.requiresPasswordConfirmation()) {
        resolve()
        return
      }

      OC.PasswordConfirmation.requirePasswordConfirmation(() => {})

      if (++tries !== 10) {
        setTimeout(() => {
          execute()
        }, 2000)
      } else {
        reject()
      }
    }

    execute()
  })
}

export { waitPasswordConfirmation }
