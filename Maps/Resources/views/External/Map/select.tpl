{* Purpose of this template: Display a popup selector for Forms and Content integration *}
{assign var='baseID' value='map'}
<div id="itemSelectorInfo" class="hidden" data-base-id="{$baseID}" data-selected-id="{$selectedId|default:0}"></div>
<div class="row">
    <div class="col-sm-8">
        <div class="form-group">
            <label for="{$baseID}Id" class="col-sm-3 control-label">{gt text='Map'}:</label>
            <div class="col-sm-9">
                <select id="{$baseID}Id" name="id" class="form-control">
                    {foreach item='map' from=$items}
                        <option value="{$map->getKey()}"{if $selectedId eq $map->getKey()} selected="selected"{/if}>{$map->getName()}</option>
                    {foreachelse}
                        <option value="0">{gt text='No entries found.'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="{$baseID}Sort" class="col-sm-3 control-label">{gt text='Sort by'}:</label>
            <div class="col-sm-9">
                <select id="{$baseID}Sort" name="sort" class="form-control">
                    <option value="workflowState"{if $sort eq 'workflowState'} selected="selected"{/if}>{gt text='Workflow state'}</option>
                    <option value="name"{if $sort eq 'name'} selected="selected"{/if}>{gt text='Name'}</option>
                    <option value="author"{if $sort eq 'author'} selected="selected"{/if}>{gt text='Author'}</option>
                    <option value="mapFile"{if $sort eq 'mapFile'} selected="selected"{/if}>{gt text='Map file'}</option>
                    <option value="testState"{if $sort eq 'testState'} selected="selected"{/if}>{gt text='Test state'}</option>
                    <option value="game"{if $sort eq 'game'} selected="selected"{/if}>{gt text='Game'}</option>
                    <option value="sizeMap"{if $sort eq 'sizeMap'} selected="selected"{/if}>{gt text='Size map'}</option>
                    <option value="bUnderground"{if $sort eq 'bUnderground'} selected="selected"{/if}>{gt text='B underground'}</option>
                    <option value="languageMap"{if $sort eq 'languageMap'} selected="selected"{/if}>{gt text='Language map'}</option>
                    <option value="createDat"{if $sort eq 'createDat'} selected="selected"{/if}>{gt text='Create dat'}</option>
                    <option value="versionMap"{if $sort eq 'versionMap'} selected="selected"{/if}>{gt text='Version map'}</option>
                    <option value="difficulty"{if $sort eq 'difficulty'} selected="selected"{/if}>{gt text='Difficulty'}</option>
                    <option value="nHumans"{if $sort eq 'nHumans'} selected="selected"{/if}>{gt text='N humans'}</option>
                    <option value="nPlayers"{if $sort eq 'nPlayers'} selected="selected"{/if}>{gt text='N players'}</option>
                    <option value="gameType"{if $sort eq 'gameType'} selected="selected"{/if}>{gt text='Game type'}</option>
                    <option value="mapStyle"{if $sort eq 'mapStyle'} selected="selected"{/if}>{gt text='Map style'}</option>
                    <option value="description"{if $sort eq 'description'} selected="selected"{/if}>{gt text='Description'}</option>
                    <option value="foreground"{if $sort eq 'foreground'} selected="selected"{/if}>{gt text='Foreground'}</option>
                    <option value="underground"{if $sort eq 'underground'} selected="selected"{/if}>{gt text='Underground'}</option>
                    <option value="scoreRev"{if $sort eq 'scoreRev'} selected="selected"{/if}>{gt text='Score rev'}</option>
                    <option value="nScoreRev"{if $sort eq 'nScoreRev'} selected="selected"{/if}>{gt text='N score rev'}</option>
                    <option value="nDownloads"{if $sort eq 'nDownloads'} selected="selected"{/if}>{gt text='N downloads'}</option>
                    <option value="createdDate"{if $sort eq 'createdDate'} selected="selected"{/if}>{gt text='Creation date'}</option>
                    <option value="createdBy"{if $sort eq 'createdBy'} selected="selected"{/if}>{gt text='Creator'}</option>
                    <option value="updatedDate"{if $sort eq 'updatedDate'} selected="selected"{/if}>{gt text='Update date'}</option>
                    <option value="updatedBy"{if $sort eq 'updatedBy'} selected="selected"{/if}>{gt text='Updater'}</option>
                </select>
                <select id="{$baseID}SortDir" name="sortdir" class="form-control">
                    <option value="asc"{if $sortdir eq 'asc'} selected="selected"{/if}>{gt text='ascending'}</option>
                    <option value="desc"{if $sortdir eq 'desc'} selected="selected"{/if}>{gt text='descending'}</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="{$baseID}SearchTerm" class="col-sm-3 control-label">{gt text='Search for'}:</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <input type="text" id="{$baseID}SearchTerm" name="q" class="form-control" />
                    <span class="input-group-btn">
                        <input type="button" id="tdMMapsModuleSearchGo" name="gosearch" value="{gt text='Filter'}" class="btn btn-default" />
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div id="{$baseID}Preview" style="border: 1px dotted #a3a3a3; padding: .2em .5em">
            <p><strong>{gt text='Map information'}</strong></p>
            {img id='ajaxIndicator' modname='core' set='ajax' src='indicator_circle.gif' alt='' class='hidden'}
            <div id="{$baseID}PreviewContainer">&nbsp;</div>
        </div>
    </div>
</div>
