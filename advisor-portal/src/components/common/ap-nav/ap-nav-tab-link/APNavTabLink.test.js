import React from 'react';
import ReactDOM from 'react-dom';
import APNavTabLink from './APNavTabLink';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APNavTabLink />, div);
  ReactDOM.unmountComponentAtNode(div);
});