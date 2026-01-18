import React from 'react';
import ReactDOM from 'react-dom';
import APNavTabPanelContainer from './APNavTabPanelContainer';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APNavTabPanelContainer />, div);
  ReactDOM.unmountComponentAtNode(div);
});