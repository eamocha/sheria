import React from 'react';
import ReactDOM from 'react-dom';
import HearingsRowMenu from './HearingsRowMenu';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<HearingsRowMenu />, div);
  ReactDOM.unmountComponentAtNode(div);
});