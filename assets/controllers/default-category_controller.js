import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static values = { url:String };


  async set() {
    const response = await fetch(this.urlValue);
//    const respStatus = await response.status;
    if (response.status === 204) {
      this.dispatch('success');
    } else {
      location.reload();
    }
  }
}
