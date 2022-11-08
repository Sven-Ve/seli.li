import { Controller } from '@hotwired/stimulus';
import { useDispatch } from 'stimulus-use';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static values = { url:String };

  connect() {
    useDispatch(this);
  }

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
