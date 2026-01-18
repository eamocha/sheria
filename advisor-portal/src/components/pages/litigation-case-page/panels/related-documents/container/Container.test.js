import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePageGeneralInfoPanelContainer from './LitigationCasePageGeneralInfoPanelContainer';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePageGeneralInfoPanelContainer />, div);
  ReactDOM.unmountComponentAtNode(div);
});