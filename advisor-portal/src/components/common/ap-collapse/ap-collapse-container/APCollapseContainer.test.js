import React from 'react';
import ReactDOM from 'react-dom';
import APCollapseContainer from './APCollapseContainer';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APCollapseContainer />, div);
  ReactDOM.unmountComponentAtNode(div);
});