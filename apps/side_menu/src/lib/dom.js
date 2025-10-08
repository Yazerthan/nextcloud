/**
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

const waitContainer = async (selector) => {
  return new Promise((resolve) => {
    const execute = () => {
      const container = document.querySelector(selector)

      if (container) {
        resolve(container)
      } else {
        setTimeout(() => {
          execute(selector)
        }, 50)
      }
    }

    execute(selector)
  })
}

const createElement = (tagName, attributes) => {
  const element = document.createElement(tagName)

  if (typeof attributes === 'object') {
    for (let i in attributes) {
      if (i === 'text') {
        element.textContent = attributes[i]
      } else if (i === 'html') {
        element.innerHTML = attributes[i]
      } else {
        element.setAttribute(i, attributes[i])
      }
    }
  }

  return element
}

export { waitContainer, createElement }
